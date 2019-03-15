<?php

namespace Bonnier\Willow\Base\Commands;

class CommandBootstrap
{
    const CORE_CMD_NAMESPACE = 'willow';

    public function __construct()
    {
        if (defined('WP_CLI') && WP_CLI) {
            if (!defined('PLL_ADMIN')) {
                define('PLL_ADMIN', true); // Tell Polylang to be in admin mode so that various term filters are loaded
            }
            Cleanup::register();
            AuthorFix::register();
            CxenseSync::register();
            Sitemap::register();
        }
    }
}
