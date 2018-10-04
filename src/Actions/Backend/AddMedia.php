<?php

namespace Bonnier\Willow\Base\Actions\Backend;

/**
 * Class AddMedia
 *
 * @package \Bonnier\Willow\Base\Actions\Backend
 */
class AddMedia
{
    public function __construct()
    {
        add_filter('wp_update_attachment_metadata', [$this, 'wpUpdateAttachmentMetadata'], 120, 2);
    }

    public function wpUpdateAttachmentMetadata($data, $attachment_id)
    {
        // Get imgix color palette and save it in post_meta
        $data['imgix_palette'] = file_get_contents(wp_get_attachment_url($attachment_id) . '?palette=json');
    }
}
