<?php

namespace Bonnier\Willow\Base\Actions\Universal;

use Bonnier\WP\ContentHub\Editor\Models\WpComposite;
use Bonnier\WP\SiteManager\WpSiteManager;

/**
 * Class PageTemplates
 *
 * @package \Bonnier\Willow\Base\Actions\Frontend
 */
class PageTemplates
{
    public $pageTemplates;
    public $compositeTemplates;
    //TODO: implement logic to handle sub-themes
    private static $instance;

    private function __construct()
    {
        $this->pageTemplates = [
            'frontpage' => 'Frontpage',
            'cookiepolicy' => 'Cookie Politik',
        ];
        $this->compositeTemplates = [];
        if (($site = WpSiteManager::instance()->settings()->getSite()) &&
            $brand = data_get($site, 'brand.brand_code')) {
            switch ($brand) {
                case 'BOB':
                    $this->pageTemplates = array_merge($this->pageTemplates, [
                        'authorlist' => 'Author List',
                        'architonic' => 'Architonic iFrame',
                        ]);
                    $this->compositeTemplates = array_merge($this->compositeTemplates, [
                        'bodum-stempel' => 'Bodum Stempel',
                        'bodum-pour-over' => 'Bodum Pour over',
                        'bodum-vacuum' => 'Bodum Vacuum',
                    ]);
                    break;
                case 'ILL':
                    $this->compositeTemplates = array_merge($this->compositeTemplates, [
                        'gradient' => 'Gradient',
                        'colorblock' => 'Farveblok',
                    ]);
                    break;
                default:
                    $this->pageTemplates = array_merge($this->pageTemplates, [
                        'authorlist' => 'Author List',
                        'architonic' => 'Architonic iFrame',
                    ]);
                    $this->compositeTemplates = array_merge($this->compositeTemplates, [
                        'bodum-stempel' => 'Bodum Stempel',
                        'bodum-pour-over' => 'Bodum Pour over',
                        'bodum-vacuum' => 'Bodum Vacuum',
                        'gradient' => 'Gradient',
                        'colorblock' => 'Farveblok',
                    ]);
                    break;
            }
        }
        $postType = WpComposite::POST_TYPE;
        // Adds our template to the page dropdown for v4.7+
        add_filter('theme_page_templates', [$this, 'addTemplatesToMetaBox']);
        add_filter("theme_{$postType}_templates", [$this, 'addTemplatesToContenthubComposites']);
        // Add a filter to the save post to inject out template into the page cache
        add_filter('wp_insert_post_data', [$this, 'registerProjectTemplates']);
    }

    /**
     * Adds our template to the page dropdown for v4.7+
     * @param $postTemplates
     * @return array
     */
    public function addTemplatesToMetaBox($postTemplates)
    {
        // Add a filter to the wp 4.7 version attributes metabox
        $postTemplates = array_merge($postTemplates, $this->pageTemplates);
        return $postTemplates;
    }

    public function addTemplatesToContenthubComposites($templates)
    {
        return array_merge($templates, $this->compositeTemplates);
    }

    /**
     * Adds our template to the pages cache in order to trick WordPress
     * into thinking the template file exists where it doens't really exist.
     * @param $atts
     * @return string
     */
    public function registerProjectTemplates($atts)
    {

        // Create the key used for the themes cache
        $cache_key = 'page_templates-' . md5(get_theme_root() . '/' . get_stylesheet());

        // Retrieve the cache list.
        // If it doesn't exist, or it's empty prepare an array
        $templates = wp_get_theme()->get_page_templates();
        if (empty($templates)) {
            $templates = array();
        }

        // New cache, therefore remove the old one
        wp_cache_delete($cache_key, 'themes');

        // Now add our template to the list of templates by merging our templates
        // with the existing templates array from the cache.
        $templates = array_merge($templates, $this->pageTemplates);

        // Add the modified cache to allow WordPress to pick it up for listing
        // available templates
        wp_cache_add($cache_key, $templates, 'themes', 1800);

        return $atts;
    }

    /**
     * Returns an instance of this class.
     */
    public static function get_instance()
    {
        if (null == self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
