<?php

namespace Bonnier\Willow\Base\Actions;

use Bonnier\Willow\Base\Actions\Backend\PreviewUrl;
use Bonnier\Willow\Base\Actions\Frontend\Assets;
use Bonnier\Willow\Base\Actions\Frontend\Imgix;
use Bonnier\Willow\Base\Actions\Frontend\Init;
use Bonnier\Willow\Base\Actions\Frontend\MetaTags;
use Bonnier\Willow\Base\Actions\Universal\PageTemplates;
use Bonnier\Willow\Base\Actions\Universal\PolylangTranslations;
use Bonnier\Willow\Base\Actions\Universal\ThemeSettings;
use Bonnier\Willow\Base\Actions\Frontend\Header;
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
        if (!is_admin()) {
            new Init();
            new MetaTags();
            new Assets();
            new Imgix();
            new Header();
        }

        // Universal
        new ThemeSettings();
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
