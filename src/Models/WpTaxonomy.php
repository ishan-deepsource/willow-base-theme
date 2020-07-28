<?php

namespace Bonnier\Willow\Base\Models;

use Illuminate\Support\Collection;

class WpTaxonomy
{
    private const CUSTOM_TAXONOMIES_OPTION = 'content_hub_custom_taxonomies';

    public static function register()
    {
        add_action('init', function () {
            static::get_custom_taxonomies()->each(function ($customTaxonomy) {
                register_taxonomy($customTaxonomy->machine_name, WpComposite::POST_TYPE, [
                    'label'             => $customTaxonomy->name,
                    'show_ui'           => false,
                    'show_admin_column' => false,
                ]);
            });
        });
    }

    public static function add($externalTaxonomy)
    {
        $customTaxonomies = static::get_custom_taxonomies();
        $customTaxonomies[$externalTaxonomy->content_hub_id] = $externalTaxonomy;
        static::set_custom_taxonomies($customTaxonomies);
    }

    public static function get_taxonomy($contentHubId)
    {
        return static::get_custom_taxonomies()->get($contentHubId)->machine_name ?? null;
    }

    public static function get_custom_taxonomies()
    {
        return collect(get_option(static::CUSTOM_TAXONOMIES_OPTION, []));
    }

    public static function set_custom_taxonomies(Collection $taxonomies)
    {
        update_option(static::CUSTOM_TAXONOMIES_OPTION, $taxonomies->toArray(), true);
    }

    public static function admin_enqueue_scripts()
    {
        wp_enqueue_script(
            'acf-taxonomy-fields',
            get_theme_file_uri('/assets/js/acf/fields/taxonomy-fields.js'),
            [],
            filemtime(get_theme_file_path('/assets/js/acf/fields/taxonomy-fields.js'))
        );
    }
}
