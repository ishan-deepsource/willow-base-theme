<?php

namespace Bonnier\Willow\Base\Actions\Universal;

class ImgixSettings
{
    public function __construct()
    {
        // Disable generation of image sizes on upload
        add_filter('intermediate_image_sizes_advanced', [$this, 'intermediateImageSizes']);
    }

    public function intermediateImageSizes($sizes)
    {
        return [];
    }
}
