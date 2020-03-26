<?php

namespace Bonnier\Willow\Base\Actions\Backend;

/**
 * Class MediaThumbs
 *
 * @package \Bonnier\Willow\Base\Actions\Backend
 */
class MediaThumbs
{
    public function __construct()
    {
        // Add imgix params to thumbs so media panel thumbs does not load full size images

        add_filter('wp_get_attachment_image_src', function ($image)
        {
            $image[0] .= sprintf('?auto=compress&w=%d&h=%d', $image[1], $image[2]);
            return $image;
        });

        add_filter('wp_prepare_attachment_for_js', function($response, $attachment, $meta) {
            $response['sizes']['medium'] = [
                'url' => $response['url'] . '?auto=compress&w=130&h=130'
            ];
            return $response;
        }, 10, 3);
    }

}
