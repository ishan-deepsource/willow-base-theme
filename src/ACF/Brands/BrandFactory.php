<?php

namespace Bonnier\Willow\Base\ACF\Brands;

use Illuminate\Support\Arr;

class BrandFactory
{
    private static $mapping = [
        'bob' => BOB::class,
        'cos' => COS::class,
        'gds' => GDS::class,
        'his' => HIS::class,
        'ill' => ILL::class,
    ];

    public static function register(?string $brandCode = null)
    {
        if (is_null($brandCode)) {
            return;
        }
        $formattedBrandCode = strtolower($brandCode);

        /** @var BrandInterface | null $class */
        if ($class = Arr::get(self::$mapping, $formattedBrandCode)) {
            $class::register();
        }
    }
}
