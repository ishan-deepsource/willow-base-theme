<?php

namespace Bonnier\Willow\Base\Commands;

use WP_CLI;
use WP_CLI_Command;

class CategoryFix extends WP_CLI_Command
{
    private const CMD_NAMESPACE = 'category';

    public static function register()
    {
        WP_CLI::add_command(static::CMD_NAMESPACE, __CLASS__);
    }

    /**
     * Deletes all duplicate categories per locale with no contenthub ID and no content and writes categories with content to csv for the editors to fix manually.
     *
     * ## OPTIONS
     * <locale>
     * : The locale you want to use to find categories.
     * ## EXAMPLES
     * wp category fix da
     *
     */
    public function fix($args)
    {
        if (count($args) < 1) {
            WP_CLI::error('please provide locale:');
            WP_CLI::error(sprintf('%s category fix da', CommandBootstrap::CORE_CMD_NAMESPACE));
        }

        $locale = $args[0];

        $categories = get_terms(array(
            'taxonomy' => 'category',
            'hide_empty' => false,
        ));

        $categoriesArray = [];
        $uploadDir = wp_upload_dir();
        $filename = sprintf('/bobedre-categories-%s.csv', time());
        $filepath = rtrim($uploadDir['path'], '/') . $filename;
        $csv = fopen($filepath, "w");
        fputcsv($csv, ['ID', 'Name', 'Antal artikler', 'Slug']);
        $localizedCategories = 0;

        foreach ($categories as $category) {
            WP_CLI::log(sprintf('Checking %s: %s', $category->term_id, $category->name));
            if (pll_get_term_language($category->term_id) === $locale) {
                WP_CLI::log('Category matches locale');
                $localizedCategories++;
                if (!isset(get_term_meta($category->term_id)['content_hub_id'])) {
                    WP_CLI::log('Category has no contenthub id');
                    if ($category->count === 0) {
                        WP_CLI::log('Category has no attached posts');
                        $this->cleanupTermTaxonomyRelationships($category->term_id);
                        WP_CLI::log(sprintf('Deleting %s: %s (%s)', $category->term_id, $category->name, $category->slug));
                        wp_delete_term($category->term_id, 'category');
                    } else {
                        WP_CLI::log('Category has attached objects');
                        WP_CLI::log(sprintf('Skipping %s: %s', $category->term_id, $category->name));
                        $categoryString = $category->term_id . ',' . $category->name . ',' . $category->count . ',' . $category->slug;

                        fputcsv($csv, explode(',',$categoryString));
                        array_push($categoriesArray, $categoryString);
                    }
                }
            }
            WP_CLI::log(sprintf('-------- Done checking %s: %s --------', $category->term_id, $category->name));
        }
        WP_CLI::log(sprintf('Total categories checked: %s', $localizedCategories));
        WP_CLI::success(sprintf('Saved file at: %s', $filepath));
        fclose($csv);
    }

    private function cleanupTermTaxonomyRelationships(int $termID)
    {
        global $wpdb;
        $table = $wpdb->prefix . 'term_relationships';
        $results = $wpdb->get_results(sprintf('SELECT * FROM %s WHERE term_taxonomy_id = %s', $table, $termID), ARRAY_A);
        if(!empty($results)) {
            foreach ($results as $result) {
                $objectID = $result['object_id'];
                if (is_null(get_post($objectID))) {
                    $wpdb->delete($table, ['object_id' => $objectID, 'term_taxonomy_id' => $result['term_taxonomy_id']]);
                }
            }
        }
    }
}
