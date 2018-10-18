<?php
/**
 * Created by PhpStorm.
 * User: jonask
 * Date: 17/10/18
 * Time: 13:32
 */

namespace Bonnier\Willow\Base\Helpers;


class ImgixHelper
{
    public static function getColorPalette(string $url)
    {
        if (WP_ENV !== 'testing') {
            return file_get_contents($url . '?palette=json');
        }
        return null;
    }
}