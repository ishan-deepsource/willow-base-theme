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
        'ifo' => IFO::class,
        'ill' => ILL::class,
        'vol' => VOL::class,
    ];

    public static function register(?string $brandCode = null)
    {
        if (is_null($brandCode)) {
            return;
        }
        $formattedBrandCode = self::isBrandVoldemort(strtolower($brandCode));

        /** @var BrandInterface | null $class */
        if ($class = Arr::get(self::$mapping, $formattedBrandCode)) {
            $class::register();
        }
    }

    // This returns vol (voldemort) as a wrapping brand for all brandcodes contained.
    // Once a brand is removed from voldemort (Getting more love and custom design and feel), it should be removed from here.
    // And from class PageTemplate.php as well!
    public static function isBrandVoldemort($brandCode) {
        $voldemortBrands = ['atr', 'bim', 'bol', 'dif', 'kom', 'liv', 'mhi', 'phi', 'shi', 'tar', 'wom', 'bom', 'bil'];
        return in_array($brandCode, $voldemortBrands) ? 'vol' : $brandCode;
    }
}
