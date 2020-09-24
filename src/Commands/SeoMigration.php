<?php

namespace Bonnier\Willow\Base\Commands;

use Illuminate\Support\Collection;
use function WP_CLI\Utils\make_progress_bar;

class SeoMigration extends \WP_CLI_Command
{
    private const CMD_NAMESPACE = 'seo migration';

    public static function register()
    {
        try {
            \WP_CLI::add_command(static::CMD_NAMESPACE, __CLASS__);
        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }
    }

    /**
     * Migrate Yoast post meta to Google Seo post meta
     * Steps:
     * 1. If post meta has value of _yoast_wpseo_title, _yoast_wpseo_metadesc, so copy the value to
     * seo_teaser_title, seo_teaser_description
     * 2. If post meta has no value of _yoast_wpseo_title, _yoast_wpseo_metadesc, so skip
     * 3. Replace %%sep%% %%sitename%% to empty
     * 4. Replace "| Gjør Det selv, | Tee Itse, | Gør Det Selv | Gör Det Själv"  to empty
     * 5. If seo_teaser_title or seo_teaser_description has value, it will not update.
     *
     * Command:
     * wp seo migration yoast
     *
     */
    public function yoast()
    {
        \WP_CLI::line('Fetching all Contenthub articles...');
        global $wpdb;
        $yoastTitleName       = "_yoast_wpseo_title";
        $yoastDescriptionName = "_yoast_wpseo_metadesc";
        $seoTeaserTitle       = "seo_teaser_title";
        $seoTeaserDesc        = "seo_teaser_description";
        $updatedAmount        = 0;

        $query = 'select ID, meta_key, meta_value from wp_posts left join wp_postmeta wp on wp_posts.ID = wp.post_id where post_type ="contenthub_composite" and (wp.meta_key = "'.$yoastTitleName.'" or wp.meta_key = "'.$yoastDescriptionName.'" or wp.meta_key = "'.$seoTeaserTitle.'"or wp.meta_key = "'.$seoTeaserDesc.'");';

        $result         = collect($wpdb->get_results($query));
        $groupedResults = $result->groupBy('ID');

        \WP_CLI::line(sprintf('Found %s records', number_format($groupedResults->count())));

        $bar = make_progress_bar('Migrate yoast seo records', $result->count());
        $groupedResults->each(function (Collection $groupedResult, $postId) use (
            &$bar,
            &$updatedAmount,
            $yoastTitleName,
            $yoastDescriptionName,
            $seoTeaserTitle,
            $seoTeaserDesc
        ) {
            $updated    = false;
            $metaRecord = $groupedResult->pluck('meta_value', 'meta_key');
            $bar->tick();

            $yoastTitleMetaData = $metaRecord->get($yoastTitleName);
            $yoastDescMetaData  = $metaRecord->get($yoastDescriptionName);

            // if seo_teaser_title or seo_teaser_description does already have value, it will not be updated
            if (empty($metaRecord->get($seoTeaserTitle)) && ! empty($yoastTitleMetaData)) {
                // remove site name, because it will auto add it on willow frontend
                $siteNames = [
                    '|', 'Gjør Det selv', 'Tee Itse', 'Gør Det Selv', 'Gör Det Själv', '%%sep%%', '%%sitename%%'
                ];
                $metaTitleData = str_ireplace($siteNames, '', $yoastTitleMetaData);
                update_post_meta($postId, $seoTeaserTitle, trim($metaTitleData));
                $updated = true;
            }

            if (empty($metaRecord->get($seoTeaserDesc)) && ! empty($yoastDescMetaData)) {
                update_post_meta($postId, $seoTeaserDesc, $yoastDescMetaData);
                $updated = true;
            }

            if ($updated) {
                $updatedAmount++;
            }
        });
        $bar->finish();
        \WP_CLI::line(sprintf('Updated %s records', $updatedAmount));
    }
}
