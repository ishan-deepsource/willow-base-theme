<?php

namespace Bonnier\Willow\Base\Models;

use Bonnier\Willow\Base\Controllers\Root\CompositeRestController;
use Bonnier\Willow\Base\Helpers\EstimatedReadingTime;
use Bonnier\Willow\Base\Helpers\PermalinkHelper;
use Bonnier\Willow\Base\Helpers\Utils;
use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Composite\MetaFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Composite\TaxonomyFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Composite\TeaserFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Composite\TranslationStateFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Page\PageFieldGroup;
use Bonnier\Willow\Base\Repositories\SiteManager\SiteRepository;
use Bonnier\Willow\Base\Services\SiteManagerService;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\Willow\Base\Models\ACF\Composite\GuideMetaFieldGroup;

class WpComposite
{
    public const POST_TYPE = 'contenthub_composite';
    public const POST_TYPE_NAME = 'Content';
    public const POST_TYPE_NAME_SINGULAR = 'Composite';
    public const POST_SLUG = '%category%';
    public const POST_PERMALINK_STRUCTURE = '/%category%/%postname%';
    public const CATEGORY_BASE = '';
    public const POST_META_CONTENTHUB_ID = 'contenthub_id';
    public const POST_META_WHITE_ALBUM_ID = 'white_album_id';
    public const POST_META_WHITE_ALBUM_SOURCE = 'white_album_source';
    public const POST_META_CUSTOM_PERMALINK = 'custom_permalink';
    public const POST_TEASER_TITLE = 'teaser_title';
    public const POST_TEASER_DESCRIPTION = 'teaser_description';
    public const POST_TEASER_IMAGE = 'teaser_image';
    public const POST_META_TITLE = 'seo_teaser_title';
    public const POST_META_DESCRIPTION = 'seo_teaser_description';
    public const POST_CANONICAL_URL = 'canonical_url';
    public const POST_FACEBOOK_TITLE = 'fb_teaser_title';
    public const POST_FACEBOOK_DESCRIPTION = 'fb_teaser_description';
    public const POST_FACEBOOK_IMAGE = 'fb_teaser_image';
    public const POST_TWITTER_TITLE = 'tw_teaser_title';
    public const POST_TWITTER_DESCRIPTION = 'tw_teaser_description';
    public const POST_TWITTER_IMAGE = 'tw_teaser_image';
    public const POST_AUTHOR_DESCRIPTION = 'author_description';
    public const POST_OTHER_AUTHORS = 'other_authors';
    public const SLUG_CHANGE_HOOK = 'contenthub_composite_slug_change';

    /**
     * Register the composite as a custom wp post type
     */
    public static function register()
    {
        if (! class_exists('willow_permalinks')) {
            add_action('init', array(PermalinkHelper::class, 'instance'), 0);
        }
        add_filter('pre_option_permalink_structure', function ($currentSetting) {
            return static::POST_PERMALINK_STRUCTURE;
        });

        add_filter('pre_option_category_base', function ($currentSetting) {
            return static::CATEGORY_BASE;
        });

        add_action('init', function () {
            register_post_type(
                static::POST_TYPE,
                [
                    'labels' => [
                        'name' => __(static::POST_TYPE_NAME),
                        'singular_name' => __(static::POST_TYPE_NAME_SINGULAR)
                    ],
                    'public' => true,
                    'rest_base' => 'composites',
                    'rest_controller_class' => CompositeRestController::class,
                    'show_in_rest' => true, // enable rest api
                    'rewrite' => [
                        'willow_custom_permalink' => static::POST_PERMALINK_STRUCTURE,
                    ],
                    'has_archive' => false,
                    'supports' => [
                        'title',
                        'author'
                    ],
                    'taxonomies' => [
                        'category',
                        'post_tag'
                    ],
                ]
            );
            static::registerACFFields();
            static::loadTeaserCharacterCounter();
        });

        add_action('save_post', [__CLASS__, 'onSave'], 10, 2);
        add_action('save_post', [__CLASS__, 'onSaveSlugChange'], 5, 2);
        add_action('added_term_relationship', [__CLASS__, 'addedTermRelationship'], 10, 3);
        add_action('acf/save_post', [EstimatedReadingTime::class, 'addEstimatedReadingTime'], 20);
        add_filter('pll_copy_post_metas', [__CLASS__, 'checkIfTermIsTranslated'], 10, 3);
    }

    /**
     * @param $contenthubID
     *
     * @return null|string
     */
    public static function postIDFromContenthubID($contenthubID)
    {
        global $wpdb;
        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT post_id FROM wp_postmeta WHERE meta_key=%s AND meta_value=%s",
                static::POST_META_CONTENTHUB_ID,
                $contenthubID
            )
        );
    }

    /**
     * @param $whiteAlbumID
     *
     * @return null|string
     */
    public static function postIDFromWhiteAlbumID($whiteAlbumID)
    {
        global $wpdb;
        return $wpdb->get_var(
            $wpdb->prepare(
                "SELECT post_id FROM wp_postmeta WHERE meta_key=%s AND meta_value=%s",
                static::POST_META_WHITE_ALBUM_ID,
                $whiteAlbumID
            )
        );
    }

    /**
     * @param $postID
     *
     * @return null|string
     */
    public static function whiteAlbumIDFromPostID($postID)
    {
        return get_post_meta($postID, static::POST_META_WHITE_ALBUM_ID, true);
    }

    private static function registerACFFields()
    {
        TeaserFieldGroup::register();
        MetaFieldGroup::register();
        CompositeFieldGroup::register();
        GuideMetaFieldGroup::register();
        TranslationStateFieldGroup::register();
        TaxonomyFieldGroup::register(WpTaxonomy::get_custom_taxonomies());
    }

    public static function onSave($postId, \WP_Post $post)
    {
        $domain = Utils::removeApiSubdomain(LanguageProvider::getHomeUrl());
        if (
            static::postTypeMatchAndNotAutoDraft($post) &&
            !get_post_meta($postId, static::POST_META_CONTENTHUB_ID, true) &&
            $site = SiteRepository::find_by_domain(parse_url($domain, PHP_URL_HOST))
        ) {
            $contentHubId = base64_encode(sprintf('COMPOSITES-%s-%s', $site->brand->brand_code, $postId));
            update_post_meta($postId, WpComposite::POST_META_CONTENTHUB_ID, $contentHubId);
        }
    }


    /**
     * Triggers the slug change hook on post save
     *
     * @param          $postId
     * @param \WP_Post $post
     */
    public static function onSaveSlugChange($postId, \WP_Post $post)
    {
        remove_action('save_post', [__CLASS__, 'onSaveSlugChange'], 5);
        if (static::postTypeMatchAndNotAutoDraft($post)) {
            $oldLink = get_permalink();
            if ($oldLink && acf_validate_save_post()) {  // Validate acf input and get old link
                acf_save_post($postId);
                self::ifPostTitleIsEmptyCopyTeaserTitle($postId, $post);
                $newLink = get_permalink($postId);
                if ($newLink && $newLink !== $oldLink) { // Check if old link differ from new
                    if (stripos($oldLink, '?post_type=contenthub_composite&p=') !== false) { // Check if is first article publish.
                        $newTitle = self::sanitizeTitle($postId, $post);
                        wp_update_post([
                            'ID' => $postId,
                            'post_name' => $newTitle
                        ]);
                    }
                    do_action(static::SLUG_CHANGE_HOOK, $postId, $oldLink, $newLink); // Trigger the hook
                }
            }
        }
        add_action('save_post', [__CLASS__, 'onSaveSlugChange'], 5, 2);
    }

    public static function ifPostTitleIsEmptyCopyTeaserTitle($postId, \WP_Post $post)
    {
        if (in_array(PageFieldGroup::$brand, ['IFO', 'GDS']) && $post->post_title == '') {
            $post->post_title = get_field(static::POST_TEASER_TITLE, $postId);
            wp_update_post($post);
        }
    }

    public static function addedTermRelationship(int $postID, int $termID, string $taxonomy)
    {
        if (
            ($taxonomy === 'category' && $post = get_post($postID)) &&
            $post instanceof \WP_Post &&
            $post->post_type === self::POST_TYPE
        ) {
            // Needed by the redirect plugin, so categories updated through wp_set_post_categories() are synced to acf
            update_field('category', $termID, $post->ID);
        }
    }

    public static function mapAll($callback)
    {
        $args = [
            'post_type' => static::POST_TYPE,
            'posts_per_page' => 100,
            'paged' => 1,
            'order' => 'ASC',
            'orderby' => 'ID'
        ];

        $posts = query_posts($args);

        while ($posts) {
            collect($posts)->each(function (\WP_Post $post) use ($callback) {
                $callback($post);
            });

            $args['paged']++;
            $posts = query_posts($args);
        }
    }

    public static function checkIfTermIsTranslated($metas, $sync, $fromPostId)
    {
        var_dump('CHECK IF TERM IS TRANSLATED');die;
        if (isset($_GET['new_lang'])) {
            $fromTerms = array(get_field('category', $fromPostId));
            $fromTags = get_field('tags', $fromPostId);
            if (is_array($fromTags)) {
                foreach ($fromTags as $tag) {
                    array_push($fromTerms, $tag);
                }
            }

            $newLanguage = $_GET['new_lang'];
            foreach ($fromTerms as $term) {
                if (is_null($term) || is_null($term->term_id)) {
                    continue;
                }
                if (!LanguageProvider::isTermTranslated($term->term_id, $newLanguage)) {
                    if ($term->taxonomy === 'category') {
                        unset($metas[array_search('category', $metas)]);
                    }

                    if ($term->taxonomy === 'post_tag') {
                        unset($metas[array_search('tags', $metas)]);
                    }
                };
            }
        }

        return $metas;
    }

    public static function loadTeaserCharacterCounter()
    {
        wp_register_script(
            'teaser-character-counter',
            get_theme_file_uri('/assets/js/teaser-character-counter.js'),
            ['acf-input', 'acf-input-markdown-editor'],
            get_theme_file_path('/assets/js/teaser-character-counter.js')
        );
        wp_enqueue_script(
            'teaser-character-counter',
            '',
            ['acf-input', 'acf-input-markdown-editor'],
            get_theme_file_path('/assets/js/teaser-character-counter.js')
        );
    }

    private static function postTypeMatchAndNotAutoDraft($post)
    {
        return is_object($post) && $post->post_type === static::POST_TYPE && $post->post_status !== 'auto-draft';
    }

    private static function sanitizeTitle($postId, \WP_Post $post)
    {
        $string = $post->post_title;
        if ( !preg_match('/[\x80-\xff]/', $string) )
            return $string;
        if (seems_utf8($string)) {
            $chars = array(
                // Decompositions for Latin-1 Supplement
                'ª' => 'a', 'º' => 'o',
                'À' => 'A', 'Á' => 'A',
                'Â' => 'A', 'Ã' => 'A',
                'Ä' => 'A', 'Å' => 'A',
                'Æ' => 'A', 'Ç' => 'C',
                'È' => 'E', 'É' => 'E',
                'Ê' => 'E', 'Ë' => 'E',
                'Ì' => 'I', 'Í' => 'I',
                'Î' => 'I', 'Ï' => 'I',
                'Ð' => 'D', 'Ñ' => 'N',
                'Ò' => 'O', 'Ó' => 'O',
                'Ô' => 'O', 'Õ' => 'O',
                'Ö' => 'O', 'Ù' => 'U',
                'Ú' => 'U', 'Û' => 'U',
                'Ü' => 'U', 'Ý' => 'Y',
                'Þ' => 'TH','ß' => 's',
                'à' => 'a', 'á' => 'a',
                'â' => 'a', 'ã' => 'a',
                'ä' => 'a', 'å' => 'a',
                'æ' => 'a', 'ç' => 'c',
                'è' => 'e', 'é' => 'e',
                'ê' => 'e', 'ë' => 'e',
                'ì' => 'i', 'í' => 'i',
                'î' => 'i', 'ï' => 'i',
                'ð' => 'd', 'ñ' => 'n',
                'ò' => 'o', 'ó' => 'o',
                'ô' => 'o', 'õ' => 'o',
                'ö' => 'o', 'ø' => 'o',
                'ù' => 'u', 'ú' => 'u',
                'û' => 'u', 'ü' => 'u',
                'ý' => 'y', 'þ' => 'th',
                'ÿ' => 'y', 'Ø' => 'O',
                // Decompositions for Latin Extended-A
                'Ā' => 'A', 'ā' => 'a',
                'Ă' => 'A', 'ă' => 'a',
                'Ą' => 'A', 'ą' => 'a',
                'Ć' => 'C', 'ć' => 'c',
                'Ĉ' => 'C', 'ĉ' => 'c',
                'Ċ' => 'C', 'ċ' => 'c',
                'Č' => 'C', 'č' => 'c',
                'Ď' => 'D', 'ď' => 'd',
                'Đ' => 'D', 'đ' => 'd',
                'Ē' => 'E', 'ē' => 'e',
                'Ĕ' => 'E', 'ĕ' => 'e',
                'Ė' => 'E', 'ė' => 'e',
                'Ę' => 'E', 'ę' => 'e',
                'Ě' => 'E', 'ě' => 'e',
                'Ĝ' => 'G', 'ĝ' => 'g',
                'Ğ' => 'G', 'ğ' => 'g',
                'Ġ' => 'G', 'ġ' => 'g',
                'Ģ' => 'G', 'ģ' => 'g',
                'Ĥ' => 'H', 'ĥ' => 'h',
                'Ħ' => 'H', 'ħ' => 'h',
                'Ĩ' => 'I', 'ĩ' => 'i',
                'Ī' => 'I', 'ī' => 'i',
                'Ĭ' => 'I', 'ĭ' => 'i',
                'Į' => 'I', 'į' => 'i',
                'İ' => 'I', 'ı' => 'i',
                'Ĳ' => 'IJ','ĳ' => 'ij',
                'Ĵ' => 'J', 'ĵ' => 'j',
                'Ķ' => 'K', 'ķ' => 'k',
                'ĸ' => 'k', 'Ĺ' => 'L',
                'ĺ' => 'l', 'Ļ' => 'L',
                'ļ' => 'l', 'Ľ' => 'L',
                'ľ' => 'l', 'Ŀ' => 'L',
                'ŀ' => 'l', 'Ł' => 'L',
                'ł' => 'l', 'Ń' => 'N',
                'ń' => 'n', 'Ņ' => 'N',
                'ņ' => 'n', 'Ň' => 'N',
                'ň' => 'n', 'ŉ' => 'n',
                'Ŋ' => 'N', 'ŋ' => 'n',
                'Ō' => 'O', 'ō' => 'o',
                'Ŏ' => 'O', 'ŏ' => 'o',
                'Ő' => 'O', 'ő' => 'o',
                'Œ' => 'OE','œ' => 'oe',
                'Ŕ' => 'R','ŕ' => 'r',
                'Ŗ' => 'R','ŗ' => 'r',
                'Ř' => 'R','ř' => 'r',
                'Ś' => 'S','ś' => 's',
                'Ŝ' => 'S','ŝ' => 's',
                'Ş' => 'S','ş' => 's',
                'Š' => 'S', 'š' => 's',
                'Ţ' => 'T', 'ţ' => 't',
                'Ť' => 'T', 'ť' => 't',
                'Ŧ' => 'T', 'ŧ' => 't',
                'Ũ' => 'U', 'ũ' => 'u',
                'Ū' => 'U', 'ū' => 'u',
                'Ŭ' => 'U', 'ŭ' => 'u',
                'Ů' => 'U', 'ů' => 'u',
                'Ű' => 'U', 'ű' => 'u',
                'Ų' => 'U', 'ų' => 'u',
                'Ŵ' => 'W', 'ŵ' => 'w',
                'Ŷ' => 'Y', 'ŷ' => 'y',
                'Ÿ' => 'Y', 'Ź' => 'Z',
                'ź' => 'z', 'Ż' => 'Z',
                'ż' => 'z', 'Ž' => 'Z',
                'ž' => 'z', 'ſ' => 's',
                // Decompositions for Latin Extended-B
                'Ș' => 'S', 'ș' => 's',
                'Ț' => 'T', 'ț' => 't',
                // Euro Sign
                '€' => 'E',
                // GBP (Pound) Sign
                '£' => '',
                // Vowels with diacritic (Vietnamese)
                // unmarked
                'Ơ' => 'O', 'ơ' => 'o',
                'Ư' => 'U', 'ư' => 'u',
                // grave accent
                'Ầ' => 'A', 'ầ' => 'a',
                'Ằ' => 'A', 'ằ' => 'a',
                'Ề' => 'E', 'ề' => 'e',
                'Ồ' => 'O', 'ồ' => 'o',
                'Ờ' => 'O', 'ờ' => 'o',
                'Ừ' => 'U', 'ừ' => 'u',
                'Ỳ' => 'Y', 'ỳ' => 'y',
                // hook
                'Ả' => 'A', 'ả' => 'a',
                'Ẩ' => 'A', 'ẩ' => 'a',
                'Ẳ' => 'A', 'ẳ' => 'a',
                'Ẻ' => 'E', 'ẻ' => 'e',
                'Ể' => 'E', 'ể' => 'e',
                'Ỉ' => 'I', 'ỉ' => 'i',
                'Ỏ' => 'O', 'ỏ' => 'o',
                'Ổ' => 'O', 'ổ' => 'o',
                'Ở' => 'O', 'ở' => 'o',
                'Ủ' => 'U', 'ủ' => 'u',
                'Ử' => 'U', 'ử' => 'u',
                'Ỷ' => 'Y', 'ỷ' => 'y',
                // tilde
                'Ẫ' => 'A', 'ẫ' => 'a',
                'Ẵ' => 'A', 'ẵ' => 'a',
                'Ẽ' => 'E', 'ẽ' => 'e',
                'Ễ' => 'E', 'ễ' => 'e',
                'Ỗ' => 'O', 'ỗ' => 'o',
                'Ỡ' => 'O', 'ỡ' => 'o',
                'Ữ' => 'U', 'ữ' => 'u',
                'Ỹ' => 'Y', 'ỹ' => 'y',
                // acute accent
                'Ấ' => 'A', 'ấ' => 'a',
                'Ắ' => 'A', 'ắ' => 'a',
                'Ế' => 'E', 'ế' => 'e',
                'Ố' => 'O', 'ố' => 'o',
                'Ớ' => 'O', 'ớ' => 'o',
                'Ứ' => 'U', 'ứ' => 'u',
                // dot below
                'Ạ' => 'A', 'ạ' => 'a',
                'Ậ' => 'A', 'ậ' => 'a',
                'Ặ' => 'A', 'ặ' => 'a',
                'Ẹ' => 'E', 'ẹ' => 'e',
                'Ệ' => 'E', 'ệ' => 'e',
                'Ị' => 'I', 'ị' => 'i',
                'Ọ' => 'O', 'ọ' => 'o',
                'Ộ' => 'O', 'ộ' => 'o',
                'Ợ' => 'O', 'ợ' => 'o',
                'Ụ' => 'U', 'ụ' => 'u',
                'Ự' => 'U', 'ự' => 'u',
                'Ỵ' => 'Y', 'ỵ' => 'y',
                // Vowels with diacritic (Chinese, Hanyu Pinyin)
                'ɑ' => 'a',
                // macron
                'Ǖ' => 'U', 'ǖ' => 'u',
                // acute accent
                'Ǘ' => 'U', 'ǘ' => 'u',
                // caron
                'Ǎ' => 'A', 'ǎ' => 'a',
                'Ǐ' => 'I', 'ǐ' => 'i',
                'Ǒ' => 'O', 'ǒ' => 'o',
                'Ǔ' => 'U', 'ǔ' => 'u',
                'Ǚ' => 'U', 'ǚ' => 'u',
                // grave accent
                'Ǜ' => 'U', 'ǜ' => 'u',
            );
            $locale = LanguageProvider::getPostLanguage($postId, 'locale');
            if ( 'de_DE' == $locale || 'de_DE_formal' == $locale || 'de_CH' == $locale || 'de_CH_informal' == $locale ) {
                $chars[ 'Ä' ] = 'Ae';
                $chars[ 'ä' ] = 'ae';
                $chars[ 'Ö' ] = 'Oe';
                $chars[ 'ö' ] = 'oe';
                $chars[ 'Ü' ] = 'Ue';
                $chars[ 'ü' ] = 'ue';
                $chars[ 'ß' ] = 'ss';
            } elseif ( 'da_DK' === $locale ) {
                $chars[ 'Æ' ] = 'Ae';
                $chars[ 'æ' ] = 'ae';
                $chars[ 'Ø' ] = 'Oe';
                $chars[ 'ø' ] = 'oe';
                $chars[ 'Å' ] = 'Aa';
                $chars[ 'å' ] = 'aa';
            } elseif ( 'ca' === $locale ) {
                $chars[ 'l·l' ] = 'll';
            } elseif ( 'sr_RS' === $locale || 'bs_BA' === $locale ) {
                $chars[ 'Đ' ] = 'DJ';
                $chars[ 'đ' ] = 'dj';
            }
            $string = strtr($string, $chars);
        } else {
            $chars = array();
            // Assume ISO-8859-1 if not UTF-8
            $chars['in'] = "\x80\x83\x8a\x8e\x9a\x9e"
                ."\x9f\xa2\xa5\xb5\xc0\xc1\xc2"
                ."\xc3\xc4\xc5\xc7\xc8\xc9\xca"
                ."\xcb\xcc\xcd\xce\xcf\xd1\xd2"
                ."\xd3\xd4\xd5\xd6\xd8\xd9\xda"
                ."\xdb\xdc\xdd\xe0\xe1\xe2\xe3"
                ."\xe4\xe5\xe7\xe8\xe9\xea\xeb"
                ."\xec\xed\xee\xef\xf1\xf2\xf3"
                ."\xf4\xf5\xf6\xf8\xf9\xfa\xfb"
                ."\xfc\xfd\xff";
            $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";
            $string = strtr($string, $chars['in'], $chars['out']);
            $double_chars = array();
            $double_chars['in'] = array("\x8c", "\x9c", "\xc6", "\xd0", "\xde", "\xdf", "\xe6", "\xf0", "\xfe");
            $double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
            $string = str_replace($double_chars['in'], $double_chars['out'], $string);
        }
        $string = sanitize_title($string);
        $string = wp_unique_post_slug($string, $postId, $post->post_status, $post->post_type, $post->post_parent);
        return $string;
    }
}
