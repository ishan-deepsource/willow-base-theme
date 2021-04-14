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
                if ($customTaxonomy->machine_name === 'editorial_type') {
                    register_taxonomy($customTaxonomy->machine_name, WpComposite::POST_TYPE, [
                        'label'             => $customTaxonomy->name,
                        'show_ui'           => false,
                        'show_admin_column' => true,
                        'show_in_rest'      => true,
                    ]);
                } else {
                    register_taxonomy($customTaxonomy->machine_name, WpComposite::POST_TYPE, [
                        'label'             => $customTaxonomy->name,
                        'show_ui'           => false,
                        'show_admin_column' => false,
                        'show_in_rest'      => true,
                    ]);
                }

            });
        });

        add_action( 'restrict_manage_posts', function ($post_type) {
            if ('contenthub_composite' !== $post_type) {
                return;
            }

            $tag_terms = get_terms( array(
                'taxonomy' => 'post_tag',
                'hide_empty' => false,
            ) );
            echo "<select name='tag' id='tag' class='postform'>";
            echo '<option value="">Show All Tags</option>';
            foreach ( $tag_terms as $term ) {
                printf(
                    '<option value="%1$s" %2$s>%3$s (%4$s)</option>',
                    $term->slug,
                    ( ( isset( $_GET['tag'] ) && ( $_GET['tag'] == $term->slug ) ) ? ' selected="selected"' : '' ),
                    $term->name,
                    $term->count
                );
            }
            echo '</select>';

            $editorial_type_terms = get_terms( array(
                'taxonomy' => 'editorial_type',
                'hide_empty' => false,
            ) );
            echo "<select name='editorial_type' id='editorial-type' class='postform'>";
            echo '<option value="">Show All Editorial Types</option>';
            foreach ( $editorial_type_terms as $term ) {
                printf(
                    '<option value="%1$s" %2$s>%3$s (%4$s)</option>',
                    $term->slug,
                    ( ( isset( $_GET['editorial_type'] ) && ( $_GET['editorial_type'] == $term->slug ) ) ? ' selected="selected"' : '' ),
                    $term->name,
                    $term->count
                );
            }
            echo '</select>';
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
