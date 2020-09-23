<?php

namespace Bonnier\Willow\Base\Commands;


use Bonnier\Willow\Base\Commands\Taxonomy\Categories;
use Bonnier\Willow\Base\Commands\Taxonomy\Tags;
use Bonnier\Willow\Base\Commands\Taxonomy\Vocabularies;

if (defined('WP_CLI') && WP_CLI) {
    // fix errors when running wp cli
    if (!isset($_SERVER['HTTP_HOST'])) {
        $_SERVER['HTTP_HOST'] = WP_HOME;
    }
}

/**
 * Class CmdManager
 */
class CmdManager
{
    public const CORE_CMD_NAMESPACE = 'contenthub editor';

    public static function register()
    {
        if (defined('WP_CLI') && WP_CLI) {
            if (!defined('PLL_ADMIN')) {
                define('PLL_ADMIN', true); // Tell Polylang to be in admin mode so that various term filters are loaded
            }
            AdvancedCustomFields::register();
            Tags::register();
            Categories::register();
            Vocabularies::register();
            WaContent::register();
            WaPanel::register();
            WaImages::register();
            WaRedirectResolver::register();
            AuthorFix::register();
            Attachments::register();
            SeoMigration::register();
        }
    }
}
