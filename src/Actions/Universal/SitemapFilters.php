<?php

namespace Bonnier\Willow\Base\Actions\Universal;

use Bonnier\WP\Sitemap\WpBonnierSitemap;

class SitemapFilters
{
    private const DISALLOWED_POST_TYPES = [
        'post'
    ];

    public function __construct()
    {
        add_filter(WpBonnierSitemap::FILTER_ALLOWED_POST_TYPES, [$this, 'allowedPostTypes']);
        add_filter(WpBonnierSitemap::FILTER_POST_ALLOWED_IN_SITEMAP, [$this, 'allowedInSitemap'], 10, 2);
    }

    public function allowedPostTypes(array $postTypes)
    {
        return array_filter($postTypes, function (string $postType) {
            return !in_array($postType, static::DISALLOWED_POST_TYPES);
        });
    }

    public function allowedInSitemap(bool $allowed, \WP_Post $post)
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
}
