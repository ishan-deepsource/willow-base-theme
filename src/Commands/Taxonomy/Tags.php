<?php

namespace Bonnier\Willow\Base\Commands\Taxonomy;

use Bonnier\Willow\Base\Commands\CmdManager;
use Bonnier\Willow\Base\Repositories\SiteManager\TagRepository;
use WP_CLI;

/**
 * Class Tags
 */
class Tags extends BaseTaxonomyImporter
{
    private const CMD_NAMESPACE = 'tags';

    public static function register()
    {
        WP_CLI::add_command(CmdManager::CORE_CMD_NAMESPACE  . ' ' . static::CMD_NAMESPACE, __CLASS__);
    }

    /**
     * Imports taxonomy from site-manager
     *
     * ## EXAMPLES
     *
     * wp contenthub editor tags import
     *
     */
    public function import()
    {
        $this->triggerImport('post_tag', [TagRepository::class, 'find_by_brand_id']);

        WP_CLI::success('Done importing tags');
    }


    /**
     * Syncs local terms with site manager
     *
     * ## EXAMPLES
     *
     * wp contenthub editor tags sync
     *
     */
    public function sync()
    {
        $this->triggerSync('post_tag', [TagRepository::class, 'find_by_content_hub_id']);

        WP_CLI::success('Done syncing Tags');
    }

    /**
     * Refreshes tag count
     *
     * ## EXAMPLES
     *
     * wp contenthub editor tags refresh
     *
     */
    public function refresh()
    {
        $ids = collect(get_tags([
            'hide_empty' => false,
        ]))->pluck('term_id')->toArray();
        wp_update_term_count($ids, 'post_tag');
        WP_CLI::success(sprintf('Post count refreshed on %s tags!', count($ids)));
    }

    /**
     * Cleans failed tags imports
     *
     * ## OPTIONS
     * [--remove-empty]
     * : Whether or not to remove empty terms
     *
     * ## EXAMPLES
     *
     * wp contenthub editor tags clean
     * @param $args
     * @param $assocArgs
     */
    public function clean($args, $assocArgs)
    {
        $this->clean_terms('post_tag', isset($assocArgs['remove-empty']));
    }
}
