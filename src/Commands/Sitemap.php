<?php

namespace Bonnier\Willow\Base\Commands;

use Bonnier\WP\ContentHub\Editor\Models\WpComposite;
use WP_Post;

class Sitemap extends \WP_CLI_Command
{
    const CMD_NAMESPACE = 'sitemap';


    public static function register()
    {
        try {
            \WP_CLI::add_command(sprintf(
                '%s %s',
                CommandBootstrap::CORE_CMD_NAMESPACE,
                self::CMD_NAMESPACE
            ), __CLASS__);
        } catch (\Exception $exception) {
            \WP_CLI::warning($exception);
        }
    }

    /**
     * Migrates sitemap setting on all composites
     *
     * ## EXAMPLES
     *     wp willow sitemap migrate
     */
    public function migrate()
    {
        \WP_CLI::line('Migrating sitemap option on composites...');
        WpComposite::map_all(function (WP_Post $composite) {
            update_field('sitemap', 0, $composite->ID);
            \WP_CLI::line(sprintf('Migrating sitemap option on composite: %s', $composite->ID));
        });
        \WP_CLI::success('Done!');
    }
}
