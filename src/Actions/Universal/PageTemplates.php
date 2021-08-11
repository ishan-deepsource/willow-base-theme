<?php

namespace Bonnier\Willow\Base\Actions\Universal;

use Bonnier\Willow\Base\Models\WpComposite;
use Bonnier\WP\SiteManager\Exceptions\SiteNotFoundException;
use Bonnier\WP\SiteManager\WpSiteManager;

/**
 * Class PageTemplates
 *
 * @package \Bonnier\Willow\Base\Actions\Frontend
 */
class PageTemplates
{
    public $pageTemplates = [ // default
        'frontpage' => 'Frontpage',
        'cookiepolicy' => 'Cookie Politik',
        '404-page' => '404 template',
    ];
    public $compositeTemplates = [];

    public function __construct()
    {
        /**
         * To add a new template, add it to the content type,
         * i.e. pageTemplates or compositeTempaltes
         * and also add it to the default case,
         * since the brandcode is not available for some reason,
         * when content is being saved.
         */
        switch ($this->getBrandCode()) {
            case 'BOB':
                $this->pageTemplates = array_merge($this->pageTemplates, [
                    'author-overview' => 'Author Overview',
                    'architonic' => 'Architonic iFrame',
                ]);
                $this->compositeTemplates = array_merge($this->compositeTemplates, [
                    'bodum-stempel' => 'Bodum Stempel',
                    'bodum-pour-over' => 'Bodum Pour over',
                    'bodum-vacuum' => 'Bodum Vacuum',
                    'gradient' => 'Gradient',
                    'top-lg' => 'Top LG',
                ]);
                break;
            case 'COS':
                $this->pageTemplates = array_merge($this->pageTemplates, [
                    'author-overview' => 'Author Overview',
                ]);
                $this->compositeTemplates = array_merge($this->compositeTemplates, [
                    'gradient' => 'Gradient',
                    'colorblock' => 'Farveblok',
                ]);
                break;
            case 'ILL':
                $this->pageTemplates = array_merge($this->pageTemplates, [
                    'profile' => 'Profile Page',
                    'favourites' => 'Favourites Page',
                    'signup' => 'Signup Page',
                    'login' => 'Login Page',
                    'piano-offer-page' => 'Piano Offer Page',
                    'author-overview' => 'Author Overview',
                ]);
                $this->compositeTemplates = array_merge($this->compositeTemplates, [
                    'gradient' => 'Gradient',
                    'colorblock' => 'Farveblok',
                    'timeline' => 'Timeline',
                ]);
                break;
            case 'HIS':
                $this->pageTemplates = array_merge($this->pageTemplates, [
                    'profile' => 'Profile Page',
                    'favourites' => 'Favourites Page',
                    'signup' => 'Signup Page',
                    'login' => 'Login Page',
                    'piano-offer-page' => 'Piano Offer Page',
                ]);
                $this->compositeTemplates = array_merge($this->compositeTemplates, [
                    'gradient' => 'Gradient',
                    'colorblock' => 'Farveblok',
                    'timeline' => 'Timeline',
                ]);
                break;
            case 'GDS':
                $this->pageTemplates = array_merge($this->pageTemplates, [
                    'profile' => 'Profile Page',
                    'favourites' => 'Favourites Page',
                    'signup' => 'Signup Page',
                    'login' => 'Login Page',
                    'piano-offer-page' => 'Piano Offer Page',
                ]);
                $this->compositeTemplates = array_merge($this->compositeTemplates, [
                    'guide' => 'Guide',
                    'test' => 'Test'
                ]);
                break;
            case 'IFO':
                $this->compositeTemplates = array_merge($this->compositeTemplates, [
                    'top-lg' => 'Top LG',
                    'food-plan' => 'Food plan',
                    'workout-video' => 'Workout video',
                    'exercise-video' => 'Exercise video',
                    'recipe' => 'Recipe',
                ]);

                $this->pageTemplates = array_merge($this->pageTemplates, [
                    'author-overview' => 'Author Overview',
                    'recipe-overview' => 'Recipe Overview',
                    'training-overview' => 'Training Overview',
                    'food-plan-overview' => 'Food Plan Overview',
                ]);
                break;
            case 'VOL':
                $this->compositeTemplates = array_merge($this->compositeTemplates, [
                    'top-lg' => 'Top LG',
                ]);

                $this->pageTemplates = array_merge($this->pageTemplates, [
                    'author-overview' => 'Author Overview',
                ]);
                break;
            default:
                $this->pageTemplates = array_merge($this->pageTemplates, [
                    'author-overview' => 'Author Overview',
                    'recipe-overview' => 'Recipe Overview',
                    'training-overview' => 'Training Overview',
                    'food-plan-overview' => 'Food Plan Overview',
                    'architonic' => 'Architonic iFrame',
                    'profile' => 'Profile Page',
                    'favourites' => 'Favourites Page',
                    'signup' => 'Signup Page',
                    'login' => 'Login Page',
                    'piano-offer-page' => 'Piano Offer Page',
                ]);
                $this->compositeTemplates = array_merge($this->compositeTemplates, [
                    'bodum-stempel' => 'Bodum Stempel',
                    'bodum-pour-over' => 'Bodum Pour over',
                    'bodum-vacuum' => 'Bodum Vacuum',
                    'gradient' => 'Gradient',
                    'colorblock' => 'Farveblok',
                    'guide' => 'Guide',
                    'timeline' => 'Timeline',
                    'top-lg' => 'Top LG',
                    'food-plan' => 'Food plan',
                    'workout-video' => 'Workout video',
                    'exercise-video' => 'Exercise video',
                    'recipe' => 'Recipe',
                    'test' => 'Test',
                ]);
                break;
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
        $cacheKey = 'page_templates-' . md5(get_theme_root() . '/' . get_stylesheet());

        // Retrieve the cache list.
        // If it doesn't exist, or it's empty prepare an array
        $templates = wp_get_theme()->get_page_templates();
        if (empty($templates)) {
            $templates = array();
        }

        // New cache, therefore remove the old one
        wp_cache_delete($cacheKey, 'themes');

        // Now add our template to the list of templates by merging our templates
        // with the existing templates array from the cache.
        $templates = array_merge($templates, $this->pageTemplates);

        // Add the modified cache to allow WordPress to pick it up for listing
        // available templates
        wp_cache_add($cacheKey, $templates, 'themes', 1800);

        return $atts;
    }

    private function getBrandCode()
    {
        try {
            if ($site = WpSiteManager::instance()->settings()->getSite()) {
                // This returns vol (voldemort) as a wrapping brand for all brandcodes contained.
                // Once a brand is removed from voldemort (Getting more love and custom design and feel), it should be removed from here.
                // And from class BrandFactory.php as well!
                $voldemortBrands = ['ATR', 'BIM', 'BOL', 'DIF', 'KOM', 'LIV', 'MHI', 'PHI', 'SHI', 'TAR', 'WOM', 'BOM', 'BIL'];
                if (in_array(data_get($site, 'brand.brand_code'), $voldemortBrands)) {
                    return 'VOL';
                }

                return data_get($site, 'brand.brand_code');
            }
        } catch (SiteNotFoundException $exception) {
            return null;
        }
        return null;
    }
}
