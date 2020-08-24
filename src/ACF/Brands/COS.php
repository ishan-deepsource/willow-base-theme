<?php

namespace Bonnier\Willow\Base\ACF\Brands;

class COS extends Brand
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
