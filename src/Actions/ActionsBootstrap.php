<?php

namespace Bonnier\Willow\Base\Actions;

use Bonnier\Willow\Base\Actions\Backend\PreviewUrl;
use Bonnier\Willow\Base\Actions\Universal\PageTemplates;
use Bonnier\Willow\Base\Actions\Universal\PolylangTranslations;
use Bonnier\Willow\Base\Actions\Universal\Navigation;

class ActionsBootstrap
{
    public function __construct()
    {
        $this->loadFrontendActions();
        $this->loadBackendActions();
    }

    public function loadFrontendActions()
    {

        // Universal
        new Navigation();
        new PolylangTranslations();
        PageTemplates::get_instance();
    }

    private function loadBackendActions()
    {
        if (is_admin()) {
            new PreviewUrl();
        }
    }
}
