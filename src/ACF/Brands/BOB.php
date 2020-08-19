<?php

namespace Bonnier\Willow\Base\ACF\Brands;

class BOB extends Brand
{
    public static function register(): void
    {
        self::removeVideoUrlFromImageWidget();
        self::removeVideoUrlFromGalleryItems();
        self::removeVideoUrlFromParagraphListWidget();
        self::removeVideoUrlFromTeaserImages();
        self::removeInventoryWidget();
    }
}
