<?php

namespace Bonnier\Willow\Base\ACF\Brands;

class HIS extends Brand
{

    public static function register(): void
    {
        self::removeVideoUrlFromImageWidget();
        self::removeVideoUrlFromParagraphListWidget();
        self::removeVideoUrlFromTeaserImages();
        self::removeInventoryWidget();
    }
}
