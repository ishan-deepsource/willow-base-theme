<?php


namespace Bonnier\Willow\Base\Commands;

class CommandBootstrap
{
    public function __construct()
    {
        if (defined('WP_CLI') && WP_CLI) {
            new SetVueChildTheme();
        }
    }
}
