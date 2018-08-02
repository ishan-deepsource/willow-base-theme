<?php

namespace Bonnier\Willow\Base\Actions\Universal;

use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\ContentHub\Editor\Models\WpTaxonomy;

class PolylangTranslations
{
    private $themeBase;

    public function __construct()
    {
        $this->setThemeBase(wp_get_theme()->template);
        $this->setTranslations();
    }

    /**
     * @return mixed
     */
    public function getThemeBase()
    {
        return $this->themeBase;
    }

    /**
     * @param mixed $themeBase
     */
    public function setThemeBase($themeBase)
    {
        $this->themeBase = $themeBase;
    }

    public function registerPolylangString($translationString)
    {
        LanguageProvider::registerStringTranslation(
            $translationString,
            $translationString,
            'Theme: ' . $this->getThemeBase()
        );
    }

    private function setTranslations()
    {
        foreach (WpTaxonomy::get_custom_taxonomies()->all() as $taxonomy) {
            $this->registerPolylangString('taxonomy_' . $taxonomy->machine_name);
        }
    }
}
