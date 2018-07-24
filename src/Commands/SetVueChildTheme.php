<?php
namespace Bonnier\Willow\Base\Commands;

use WP_CLI;
use WP_CLI_Command;

/**
 * Set vue base child theme
 *
 *
 * ## EXAMPLES
 *
 *     $ wp vue-base set theme
 *     Success: created a json file with the brand colors
 */
class SetVueChildTheme extends WP_CLI_Command
{
    public function __construct()
    {
        WP_CLI::add_command('vue-base set theme', [$this, 'setTheme']);
    }

    public function setTheme()
    {
        $childTheme = getenv('APP_CHILD_THEME') ?? null;
        if ($childTheme) {
            $indexFile = sprintf("require('components/views/%s/index')", $childTheme);
        // Todo: print require('app.scss');
        } else {
            WP_CLI::warning('VUE_CHILD_THEME not set in .env please check if this is intended');
            $indexFile = '';
        }
        $themeFile = get_template_directory() . '/src/theme.js';
        file_put_contents($themeFile, $indexFile);
    }
}
