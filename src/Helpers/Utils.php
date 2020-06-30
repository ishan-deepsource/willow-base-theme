<?php

namespace Bonnier\Willow\Base\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Utils
{
    public static function getPostTypes(): Collection
    {
        return collect(get_post_types(['public' => true]))->reject(function (string $postType) {
            return $postType === 'attachment';
        });
    }

    public static function removeApiSubdomain(string $permalink)
    {
        if (Str::contains($permalink, '://api.')) {
            return preg_replace('#://api.#', '://', $permalink);
        } elseif (Str::contains($permalink, '://native-api.')) {
            return preg_replace('#://native-api.#', '://', $permalink);
        } elseif (Str::contains($permalink, '://admin.')) {
            return preg_replace('#://admin.#', '://', $permalink);
        }
        return $permalink;
    }
}
