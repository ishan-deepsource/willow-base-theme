<?php

namespace Bonnier\Willow\Base\Actions\Universal;

use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

/**
 * Class HeaderNavigation
 *
 * @package \Bonnier\Willow\Base\Actions\Universal
 */
class Navigation
{
    const HEADER_MENU_LOCATION = 'header-primary';
    const HEADER_MENU_SUBSCRIPTION_LOCATION = 'header-secondary';
    const HEADER_MENU_TERTIARY_LOCATION = 'header-tertiary';
    const FOOTER_MENU_LOCATION = 'footer-primary';
    const APP_MENU_LOCATION = 'app-primary';
    const DEFAULT_MENU_MAPPING = [
        'Tertiary Header Navigation' => [
            'location' => self::HEADER_MENU_TERTIARY_LOCATION,
            'items' => []
        ],
        'Secondary Header Navigation' => [
            'location' => self::HEADER_MENU_SUBSCRIPTION_LOCATION,
            'items'    => [
                [
                    'menu-item-title'  => 'Get Subscription',
                    'menu-item-url'    => WP_HOME,
                    'menu-item-status' => 'publish'
                ]
            ]
        ],
        'Primary Header Navigation'   => [
            'location' => self::HEADER_MENU_LOCATION,
            'items'    => []
        ],
        'Primary Footer Navigation'   => [
            'location' => self::FOOTER_MENU_LOCATION,
            'items'    => []
        ],
        'Primary App Navigation'   => [
            'location' => self::APP_MENU_LOCATION,
            'items'    => []
        ],
    ];

    protected $existingMenus;

    /**
     * HeaderNavigation constructor.
     */
    public function __construct()
    {
        register_nav_menu(static::HEADER_MENU_LOCATION, 'Primary Header Menu');
        register_nav_menu(static::HEADER_MENU_SUBSCRIPTION_LOCATION, 'Secondary Header Menu');
        register_nav_menu(static::HEADER_MENU_TERTIARY_LOCATION, 'Tertiary Header Menu');
        register_nav_menu(static::FOOTER_MENU_LOCATION, 'Primary Footer Menu');
        register_nav_menu(static::APP_MENU_LOCATION, 'Primary App Menu');

        $this->existingMenus = collect(wp_get_nav_menus());

        if (($languages = LanguageProvider::getLanguageList()) && !empty($languages)) {
            collect($languages)->each(function ($language) {
                $this->createDefaultMenus($language->name);
            });
        } else {
            $this->createDefaultMenus();
        }
        $this->assignDefaultMenuLocations();
    }

    private function createDefaultMenus(string $language = null)
    {
        collect(self::DEFAULT_MENU_MAPPING)->each(function ($menuMapping, $menuName) use ($language) {
            if ($language) {
                $menuName = sprintf('%s - %s', $menuName, $language);
            }
            if (! $this->existingMenus->pluck('name')->contains($menuName)) {
                $this->createMenuWithItems($menuMapping, $menuName);
            }
        });
    }

    private function createMenuWithItems($menuMapping, $menuName)
    {
        //create the menu
        $menuId = wp_create_nav_menu($menuName);

        collect($menuMapping['items'])->each(function ($menuItem) use ($menuId) {
            // Create each menu item
            wp_update_nav_menu_item($menuId, 0, $menuItem);
        });
    }

    private function assignDefaultMenuLocations()
    {
        $locations = get_theme_mod('nav_menu_locations');
        collect(wp_get_nav_menus())->each(function ($menu) use (&$locations) {
            if ($location = static::DEFAULT_MENU_MAPPING[$menu->name]['location'] ?? null) {
                $locations[$location] = $menu->term_id;
            }
        });
        set_theme_mod('nav_menu_locations', $locations);
    }
}
