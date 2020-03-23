<?php

namespace Bonnier\Willow\Base\Actions;

use Bonnier\Willow\Base\Actions\Backend\Locale;
use Bonnier\Willow\Base\Actions\Backend\MediaThumbs;
use Bonnier\Willow\Base\Actions\Backend\PreviewUrl;
use Bonnier\Willow\Base\Actions\Backend\RedirectHandler;
use Bonnier\Willow\Base\Actions\Universal\ImgixSettings;
use Bonnier\Willow\Base\Actions\Universal\LocalizeApi;
use Bonnier\Willow\Base\Actions\Universal\PageTemplates;
use Bonnier\Willow\Base\Actions\Universal\PolylangTranslations;
use Bonnier\Willow\Base\Actions\Universal\Navigation;
use Bonnier\Willow\Base\Actions\Backend\AddMedia;
use Bonnier\Willow\Base\Actions\Universal\SitemapFilters;

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
        new SitemapFilters();
        new LocalizeApi();
        new Navigation();
        new PolylangTranslations();
        new PageTemplates();
        if (!is_admin()) {
            new ImgixSettings();
        }
    }

    private function loadBackendActions()
    {
        if (is_admin()) {
            new MediaThumbs();
            new PreviewUrl();
            new AddMedia();
            new Locale();
            new RedirectHandler();
        }
    }
}
