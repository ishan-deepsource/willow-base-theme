<?php

namespace Bonnier\Willow\Base\Actions\Backend;

use Bonnier\Willow\Base\Adapters\Wp\Root\ColorPaletteAdapter;
use Bonnier\Willow\Base\Helpers\ImgixHelper;

/**
 * Class AddMedia
 *
 * @package \Bonnier\Willow\Base\Actions\Backend
 */
class AddMedia
{
    public function __construct()
    {
        add_filter('wp_update_attachment_metadata', [__CLASS__, 'wpUpdateAttachmentMetadata'], 120, 2);
    }

    public static function wpUpdateAttachmentMetadata($data, $attachmentId)
    {
        // Get imgix color palette and save it in postmeta
        
        if ($imageUrl = wp_get_attachment_url($attachmentId)) {
            $palette = ImgixHelper::getColorPalette($imageUrl);
            update_post_meta($attachmentId, ColorPaletteAdapter::COLOR_PALETTE_META, serialize($palette));
        }

        return $data;
    }
}
