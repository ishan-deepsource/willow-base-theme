<?php

namespace Bonnier\Willow\Base\Commands\Taxonomy;

use Bonnier\Willow\Base\Commands\BaseCmd;
use Bonnier\Willow\Base\Commands\CmdManager;
use Bonnier\Willow\Base\Models\WpTaxonomy;
use Bonnier\Willow\Base\Repositories\SiteManager\VocabularyRepository;
use WP_Cli;

/**
 * Class Vocabularies
 */
class Vocabularies extends BaseCmd
{
    private const CMD_NAMESPACE = 'vocabularies';

    public static function register()
    {
        WP_CLI::add_command(CmdManager::CORE_CMD_NAMESPACE  . ' ' . static::CMD_NAMESPACE, __CLASS__);
    }

    /**
     * Imports taxonomy from site-manager
     *
     * ## EXAMPLES
     *
     * wp contenthub editor vocabularies import
     *
     */
    public function import()
    {
        $vocabularies = collect();
        $this->mapSites(function ($site) use ($vocabularies) {
            $this->map_vocabularies($site, function ($vocabulary) use ($vocabularies) {
                $vocabularies[$vocabulary->content_hub_id] = $vocabulary;
                WP_CLI::line('Importing: ' . $vocabulary->name);
            });
        });
        WpTaxonomy::set_custom_taxonomies($vocabularies);
        WP_CLI::success('Done importing Vocabularies');
    }

    protected function map_vocabularies($site, $callable)
    {
        $vocabularies = VocabularyRepository::find_by_brand_id($site->brand->id);

        while ($vocabularies) {
            WP_CLI::line("Begning import of page: " . $vocabularies->meta->pagination->current_page);
            collect($vocabularies->data)->each($callable);
            if (isset($vocabularies->meta->pagination->links->next)) {
                $nextPage = $vocabularies->meta->pagination->current_page + 1;
                $vocabularies = VocabularyRepository::find_by_brand_id($site->brand->id, $nextPage);
                continue;
            }
            $vocabularies = null;
        }
    }
}
