<?php

namespace Bonnier\Willow\Base\Actions;

use Bonnier\Willow\Base\Actions\Backend\PostSlugChange;
use Bonnier\Willow\Base\Actions\Backend\PreviewUrl;
use Bonnier\Willow\Base\Actions\Backend\EstimatedReadingTime;
use Bonnier\Willow\Base\Actions\Universal\ImgixSettings;
use Bonnier\Willow\Base\Actions\Universal\LocalizeApi;
use Bonnier\Willow\Base\Actions\Universal\PageTemplates;
use Bonnier\Willow\Base\Actions\Universal\PolylangSubdomain;
use Bonnier\Willow\Base\Actions\Universal\PolylangTranslations;
use Bonnier\Willow\Base\Actions\Universal\Navigation;
use Bonnier\Willow\Base\Actions\Backend\EstimatedListeningTime;
use Bonnier\Willow\Base\Actions\Backend\AddMedia;

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
        new LocalizeApi();
        new Navigation();
        new PolylangTranslations();
        PageTemplates::get_instance();
        if (!is_admin()) {
            new ImgixSettings();
        }
    }

    private function loadBackendActions()
    {
        if (is_admin()) {
            new PostSlugChange();
            new PreviewUrl();
            new EstimatedReadingTime();
            new EstimatedListeningTime();
            new AddMedia();
        }
    }
}
