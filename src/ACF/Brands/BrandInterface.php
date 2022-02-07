<?php

namespace Bonnier\Willow\Base\ACF\Brands;

interface BrandInterface
{
    public static function register(?string $brandCode = null): void;
}
