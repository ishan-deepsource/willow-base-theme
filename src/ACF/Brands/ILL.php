<?php

namespace Bonnier\Willow\Base\ACF\Brands;

class ILL extends Brand
{

    public static function register(): void
    {
        self::removeInventoryWidget();
    }
}
