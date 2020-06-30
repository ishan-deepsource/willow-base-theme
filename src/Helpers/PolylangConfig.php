<?php

namespace Bonnier\Willow\Base\Helpers;

use Bonnier\Willow\Base\Models\WpComposite;
use Bonnier\Willow\Base\Models\WpTaxonomy;

/**
 * Class PolylangConfig
 */
class PolylangConfig
{
    public static function register()
    {
        add_action('option_polylang', [__CLASS__, 'polylang_options']);
        add_filter('wp_insert_post_data', [__CLASS__, 'copyPostAuthor']);
    }

    public static function polylang_options($defaultOptions)
    {
        return array_merge(
            $defaultOptions,
            [
                'post_types' => [
                    WpComposite::POST_TYPE, // Tell polylang to enable translation for our custom content type
                ],
                // Tell polylang to enable translation for each of our custom taxonomies
                'taxonomies' => collect(WpTaxonomy::get_custom_taxonomies())->pluck('machine_name')->toArray()
            ]
        );
    }

    public static function copyPostAuthor($post)
    {
        if (
            ($fromId = array_get($_GET, 'from_post')) &&
            ($fromPost = get_post($fromId)) &&
            $fromPost instanceof \WP_Post
        ) {
            $post['post_author'] = $fromPost->post_author;
        }

        return $post;
    }
}
