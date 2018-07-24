<?php

namespace Bonnier\Willow\Base\Actions\Universal;

use Bonnier\WP\ContentHub\Editor\Models\WpTaxonomy;
use Bonnier\Willow\Base\Helpers\Translation;

class PolylangTranslations
{
    private $themeBase;

    public function __construct()
    {
        $this->setThemeBase(wp_get_theme()->template);
        $this->setTranslations(Translation::STRINGS);
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
        if (function_exists('pll_register_string')) {
            pll_register_string($translationString, $translationString, 'Theme: '. $this->getThemeBase());
        }
    }

    private function setTranslations(array $translations)
    {
        foreach (WpTaxonomy::get_custom_taxonomies()->all() as $taxonomy) {
            $this->registerPolylangString('taxonomy_' . $taxonomy->machine_name);
        }

        foreach ($translations as $translation) {
            $this->registerPolylangString($translation);
        }
    }
}
