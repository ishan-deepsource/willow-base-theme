<?php

namespace Bonnier\Willow\Base\Models;

use Bonnier\Willow\Base\Helpers\HtmlToMarkdown;
use Bonnier\Willow\Base\Helpers\MimeTypeHelper;
use Bonnier\Willow\Base\Models\ACF\Attachment\AttachmentFieldGroup;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use function DeliciousBrains\WP_Offload_S3\Aws3\GuzzleHttp\Psr7\parse_query;

class WpAttachment
{
    public const POST_META_CONTENTHUB_ID = 'contenthub_id';
    public const POST_META_COPYRIGHT = 'attachment_copyright';
    public const POST_META_COPYRIGHT_URL = 'attachment_copyright_url';

    private static $postAttachmentsParent = null;
    private static $postAttachments = null;
    private static $client = null;

    public static function register()
    {
        // Add custom copyright field to image attachments
        add_filter('attachment_fields_to_edit', [__CLASS__, 'add_copyright_field_to_media_uploader'], null, 2);
        add_filter('attachment_fields_to_save', [__CLASS__, 'add_copyright_field_to_media_uploader_save'], null, 2);

        // Make attachments private
        add_filter('wp_update_attachment_metadata', [__CLASS__, 'wp_update_attachment_metadata'], 1000, 2);
        add_action('init', function () {
            static::register_acf_fields();
        });
    }

    public static function mapAll($callback)
    {
        $args = [
            'post_type' => 'attachment',
            'posts_per_page' => 100,
            'paged' => 1,
            'order' => 'ASC',
            'orderby' => 'ID'
        ];

        $posts = get_posts($args);

        while ($posts) {
            collect($posts)->each($callback);

            $args['paged']++;
            $posts = get_posts($args);
        }
    }

    public static function wp_update_attachment_metadata($data, $postId)
    {
        $postMeta = get_post_meta($postId);

        // Check that attachment meta contains s3 info so we can set the visibility of the object
        if (isset($postMeta['amazonS3_info'][0]) && $s3Info = unserialize($postMeta['amazonS3_info'][0])) {
            static::setS3ObjectVisibility($s3Info['bucket'], $s3Info['key'], 'private');
        }

        return $data;
    }

    public static function wp_get_attachment_url($url, $post_id)
    {
        if (is_admin()) {
            // Create signed url that expires after 3600 seconds (1 hour)
            return as3cf_get_secure_attachment_url($post_id, 3600);
        }

        return $url;
    }

    /**
     * Adding a "Copyright" field to the media uploader $form_fields array
     *
     * @param array $form_fields
     * @param object $post
     *
     * @return array
     */
    public static function add_copyright_field_to_media_uploader($form_fields, $post)
    {
        // Only add copyright field to image attachments
        if (!str_contains($post->post_mime_type, 'image')) {
            return $form_fields;
        }
        $form_fields['copyright_field'] = [
            'label' => __('Copyright'),
            'input' => 'text',
            'value' => get_post_meta($post->ID, static::POST_META_COPYRIGHT, true),
        ];
        $form_fields['copyright_url_field'] = [
            'label' => __('Copyright URL'),
            'input' => 'text',
            'value' => get_post_meta($post->ID, static::POST_META_COPYRIGHT_URL, true),

        ];
        return $form_fields;
    }


    /**
     * Save our new "Copyright" field
     *
     * @param object $post
     * @param object $attachment
     *
     * @return object
     */
    public static function add_copyright_field_to_media_uploader_save($post, $attachment)
    {
        if (isset($attachment['copyright_field']) && ! empty($attachment['copyright_field'])) {
            update_post_meta($post['ID'], static::POST_META_COPYRIGHT, $attachment['copyright_field']);
            update_post_meta($post['ID'], static::POST_META_COPYRIGHT_URL, $attachment['copyright_url_field']);
        } else {
            delete_post_meta($post['ID'], static::POST_META_COPYRIGHT);
            delete_post_meta($post['ID'], static::POST_META_COPYRIGHT_URL);
        }

        return $post;
    }

    /**
     * @param $id
     *
     * @return null|string
     */
    public static function id_from_contenthub_id($id)
    {
        global $wpdb;
        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT post_id FROM wp_postmeta WHERE meta_key=%s AND meta_value=%s",
                static::POST_META_CONTENTHUB_ID,
                $id
            )
        );
    }

    /**
     * @param $postId
     *
     * @return null|string
     */
    public static function contenthub_id($postId)
    {
        return get_post_meta($postId, static::POST_META_CONTENTHUB_ID, true) ?: null;
    }

    /**
     * @param $id
     *
     * Deleted the attachment with matching contenthub id
     *
     * @return null|string
     */
    public static function delete_by_contenthub_id($id)
    {
        if ($attachmentId = static::id_from_contenthub_id($id)) {
            return wp_delete_post($attachmentId, true);
        }
        return null;
    }

    /**
     * Finds the attachment contenthub ids by fetching them through the attached posts
     *
     * @param $postId
     */
    public static function get_post_attachments($postId)
    {
        static::$postAttachmentsParent = $postId;
        static::$postAttachments = collect(get_children([
            'post_parent' => $postId,
            'post_status' => 'inherit',
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'order' => 'ASC',
            'orderby' => 'menu_order',
            'fields' => 'ids'
        ]))->reduce(function (Collection $out, $attachmentId) {
            if ($contentHubId = get_post_meta($attachmentId, self::POST_META_CONTENTHUB_ID, true)) {
                $out->put($contentHubId, $attachmentId);
            }
            return $out;
        }, collect([]));
    }

    /**
     * Upload a local file with caption
     *
     * @param int $postId
     * @param String $localeFile
     * @param String $fileName
     * @param String $caption
     *
     * @return null|int
     */
    public static function upload_file($postId, $localeFile, $fileName, $caption)
    {
        // Uploading file
        $fileContent = file_get_contents($localeFile);
        $uploadedFile = wp_upload_bits($fileName, null, $fileContent);
        if ($uploadedFile['error']) {
            //WP_CLI::error('');
            var_dump($uploadedFile);
            return null;
        }

        // Create attachment
        $fileTitle = $fileName;
        $attachment = [
            'post_mime_type' => mime_content_type($uploadedFile['file']),
            'post_parent' => $postId,
            'post_title' => $fileTitle,
            'post_content' => '',
            'post_excerpt' => $caption,
            'post_status' => 'inherit',
            /*
            'meta_input' => [
                static::POST_META_CONTENTHUB_ID => $file->id,
                static::POST_META_COPYRIGHT => $file->copyright ?? null,
                static::POST_META_COPYRIGHT_URL => $file->copyright_url ?? null,
                '_wp_attachment_image_alt' => static::getAlt($file),
            ]
            */  // TODO
        ];
        $attachmentId = wp_insert_attachment($attachment, $uploadedFile['file'], $postId);
        update_field(AttachmentFieldGroup::CAPTION_FIELD_KEY, $caption, $attachmentId);
        if (is_wp_error($attachmentId)) {
            return null;
        }

        // Attachment meta data
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        $attachmentData = wp_generate_attachment_metadata($attachmentId, $uploadedFile['file']);
        if (!wp_update_attachment_metadata($attachmentId, $attachmentData)) {
            return null;
        }

        // Set language for the image so it will be visible in Media Library
        if ($language = LanguageProvider::getPostLanguage($postId)) {
            LanguageProvider::setPostLanguage($attachmentId, $language);
        }

        return $attachmentId;
    }

    public static function upload_attachment($postId, $file)
    {
        if (is_null($file)) {
            return null;
        }

        // If attachment already exists then update meta data and return the id
        if (!is_null(static::$postAttachments) && $postId == static::$postAttachmentsParent) {
            $existingId = static::$postAttachments->get($file->id);
        } else {
            $existingId = static::id_from_contenthub_id($file->id);
        }
        if ($existingId) {
            static::updateAttachment($existingId, $file);
            return $existingId;
        }

        // Make sure that url has a scheme
        if (!isset(parse_url($file->url)['scheme'])) {
            $file->url = 'http:' . $file->url;
        }

        $rawFileName = basename($file->url);

        // Sanitize the new file name so WordPress will upload it
        $fileName = static::sanitizeFileName($rawFileName, $file->url);

        // Make sure to sanitize the file name so urls with spaces and other special chars will work
        $file->url = str_replace($rawFileName, urlencode($rawFileName), $file->url);

        // Getting file stream
        try {
            $fileResponse = static::getClient()->get(urldecode($file->url));
        } catch (\Exception $exception) {
            return null;
        }

        // No file extension, try to fix before upload
        if (!str_contains($fileName, '.')) {
            $contentType = $fileResponse->getHeader('Content-Type');
            if (!empty($contentType) && $extension = MimeTypeHelper::mimeToExtension($contentType[0])) {
                $fileName .= '.' . $extension;
            }
        }

        // Set extra mime types to allow for all image types like webp, pjpeg and jpeg2000
        add_filter('mime_types', function ($mimeTypes) {
            return array_merge($mimeTypes, MimeTypeHelper::extensionToMimeArray());
        }, 15, 2);

        // Uploading file
        $uploadedFile = wp_upload_bits($fileName, null, $fileResponse->getBody()->getContents());
        if ($uploadedFile['error']) {
            var_dump($uploadedFile);
            return null;
        }
        $post_title = $file->title;
        if (empty($post_title)){
            // file name example: narvafronten-nattstrid-andra-varldskriget-ostfronten-R-HbTkaiunaWgbbujINAOw.jpg
            $arr =explode('-',str_replace('_',' ',substr($fileName, 0, strrpos($fileName, "."))));
            if (!empty($arr)){
                //Should remove last element file seed (f.x. HbTkaiunaWgbbujINAOw)
                array_pop($arr);
                $post_title =  implode(' ', $arr);
            }else {
                $post_title = '';
            }
        }
        // Creating attachment
        $attachment = [
            'post_mime_type' => mime_content_type($uploadedFile['file']),
            'post_parent' => $postId,
            'post_title' => $post_title,
            'post_content' => '',
            'post_excerpt' => static::getCaption($file),
            'post_status' => 'inherit',
            'meta_input' => [
                static::POST_META_CONTENTHUB_ID => $file->id,
                static::POST_META_COPYRIGHT => $file->copyright ?? null,
                static::POST_META_COPYRIGHT_URL => $file->copyright_url ?? null,
                '_wp_attachment_image_alt' => static::getAlt($file),
            ]
        ];
        $attachmentId = wp_insert_attachment($attachment, $uploadedFile['file'], $postId);
        update_field(AttachmentFieldGroup::CAPTION_FIELD_KEY, static::getCaption($file), $attachmentId);
        if (is_wp_error($attachmentId)) {
            return null;
        }

        // Attachment meta data
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        $attachmentData = wp_generate_attachment_metadata($attachmentId, $uploadedFile['file']);
        if (!wp_update_attachment_metadata($attachmentId, $attachmentData)) {
            return null;
        }

        if ($language = LanguageProvider::getPostLanguage($postId)) {
            LanguageProvider::setPostLanguage($attachmentId, $language);
        }

        return $attachmentId;
    }

    private static function register_acf_fields()
    {
        AttachmentFieldGroup::register();
    }

    public static function updateAttachment($attachmentId, $file)
    {
        update_post_meta($attachmentId, '_wp_attachment_image_alt', static::getAlt($file));
        update_post_meta($attachmentId, static::POST_META_COPYRIGHT, $file->copyright ?? '');
        update_post_meta($attachmentId, static::POST_META_COPYRIGHT_URL, $file->copyright_url ?? '');
        update_field(AttachmentFieldGroup::CAPTION_FIELD_KEY, static::getCaption($file), $attachmentId);
        global $wpdb;
        $wpdb->update(
            'wp_posts',
            [
                'post_title' => $file->title ?? '',
                'post_content' => '',
                'post_excerpt' => static::getCaption($file),
            ],
            [
                'ID' => $attachmentId
            ]
        );
    }

    private static function setS3ObjectVisibility($bucket, $key, $acl)
    {
        /** @var \Amazon_S3_And_CloudFront $as3cf */
        global $as3cf;

        /** @var AWS_Provider $s3Client */
        $s3Client = $as3cf->get_s3client();

        $s3Client->update_object_acl(array(
            'ACL' => $acl,
            'Bucket' => $bucket,
            'Key' => $key
        ));
    }

    private static function sanitizeFileName($rawFileName, $url)
    {
        $sanitizedFileName = sanitize_file_name($rawFileName);
        $query = parse_query(parse_url($url, PHP_URL_QUERY));
        if (
            isset($query['fm']) &&
            $fileWithoutExt = pathinfo($sanitizedFileName, PATHINFO_FILENAME)
        ) {
            // Append correct file extension from imgix format query param
            return sprintf('%s.%s', $fileWithoutExt, $query['fm']);
        }
        // Fallback to default WP file sanitation
        return $sanitizedFileName;
    }

    private static function getAlt($file)
    {
        return static::getFirstMatchingAttribute($file, ['altText', 'alt_text', 'title', 'caption'], '');
    }

    /**
     * @param $file
     *
     * @return mixed|null|string
     */
    private static function getCaption($file)
    {
        $caption = static::getFirstMatchingAttribute($file, ['description', 'caption'], '');
        if (! empty($caption)) {
            $caption = HtmlToMarkdown::parseHtml($caption);
        }
        return $caption;
    }

    private static function getFirstMatchingAttribute($file, array $attributes, $defaultValue = null)
    {
        $firstNonEmptyAttribute = collect($attributes)->first(function ($atr) use ($file) {
            return !empty(data_get($file, $atr));
        });
        return $firstNonEmptyAttribute ? data_get($file, $firstNonEmptyAttribute) : $defaultValue;
    }

    private static function getClient(): Client
    {
        if (is_null(static::$client)) {
            static::$client =  new Client();
        }
        return static::$client;
    }
}
