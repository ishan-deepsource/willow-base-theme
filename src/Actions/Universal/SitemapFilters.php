<?php

namespace Bonnier\Willow\Base\Actions\Universal;

use Bonnier\WP\Sitemap\WpBonnierSitemap;
use Illuminate\Support\Str;

class SitemapFilters
{
    private const DISALLOWED_POST_TYPES = [
        'post'
    ];

    public function __construct()
    {
        add_filter(WpBonnierSitemap::FILTER_ALLOWED_POST_TYPES, [__CLASS__, 'allowedPostTypes']);
        add_filter(WpBonnierSitemap::FILTER_POST_ALLOWED_IN_SITEMAP, [__CLASS__, 'allowedInSitemap'], 10, 2);
        add_filter(WpBonnierSitemap::FILTER_POST_PERMALINK, [__CLASS__, 'removeApiSubdomain'], 10);
        add_filter(WpBonnierSitemap::FILTER_CATEGORY_PERMALINK, [__CLASS__, 'removeApiSubdomain'], 10);
        add_filter(WpBonnierSitemap::FILTER_TAG_PERMALINK, [__CLASS__, 'removeApiSubdomain'], 10);
    }

    public static function allowedPostTypes(array $postTypes)
    {
        return array_values(array_filter($postTypes, function (string $postType) {
            return !in_array($postType, static::DISALLOWED_POST_TYPES);
        }));
    }

    public static function allowedInSitemap(bool $allowed, \WP_Post $post)
    {
        $sitemap = get_post_meta($post->ID, 'sitemap', true);
        if ($sitemap && intval($sitemap) === 1) {
            $allowed = false;
        }
        $excludedPlatforms = get_field('exclude_platforms', $post->ID);
        if ($excludedPlatforms && in_array('web', $excludedPlatforms)) {
            $allowed = false;
        }

        return $allowed;
    }

    public static function removeApiSubdomain(string $permalink)
    {
        if (Str::contains($permalink, '://api.')) {
            return preg_replace('#://api.#', '://', $permalink);
        } else if (Str::contains($permalink, '://native-api.')) {
            return preg_replace('#://native-api.#', '://', $permalink);
        } else if (Str::contains($permalink, '://admin.')) {
            return preg_replace('#://admin.#', '://', $permalink);
        }
        return $permalink;
    }
}
