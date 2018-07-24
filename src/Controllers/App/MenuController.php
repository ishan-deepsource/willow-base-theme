<?php

namespace Bonnier\Willow\Base\Controllers\App;

use Bonnier\Willow\Base\Adapters\Wp\Root\MenuItemAdapter;
use Bonnier\Willow\Base\Helpers\Cache;
use Bonnier\Willow\Base\Models\Base\Root\MenuItem;
use Bonnier\Willow\Base\Transformers\Api\Root\MenuItemTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

class MenuController extends WP_REST_Controller
{
    public function register_routes()
    {
        register_rest_route('app', '/menus/(?P<menu>[a-z|-]+)', [
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => [$this, 'getMenu']
        ]);
        register_rest_route('app', '/menus', [
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => [$this, 'getMenus']
        ]);
    }



    /**
     * @param \WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function getMenus(WP_REST_Request $request)
    {
        $locale = pll_current_language();

        $menus = Cache::remember(
            'willow_menus_' . $locale,
            4 * HOUR_IN_SECONDS,
            function () use ($locale, $request) {
                // get the wordpress menu locations and check if the requested one is registered
                return collect(get_nav_menu_locations())->map(function ($menuId, $location) {
                    return $this->getMenuItems($menuId);
                })->toArray();
            }

        );

        return new WP_REST_Response($menus);
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function getMenu(WP_REST_Request $request)
    {
        $locale = pll_current_language();

        $menuItems = Cache::remember(
            'willow_menu_' . $request->get_param('menu')  . $locale,
            4 * HOUR_IN_SECONDS,
            function () use ($locale, $request) {
                // get the wordpress menu locations and check if the requested one is registered
                $locations = get_nav_menu_locations();
                if (!array_key_exists($request->get_param('menu'), $locations)) {
                    return new  WP_Error(
                        'menu_location_does_not_exist',
                        __('The menu location you are looking for does not exist'),
                        array( 'status' => 404 )
                    );
                }

                return $this->getMenuItems($locations[$request->get_param('menu')]);
            }
        );

        return new WP_REST_Response($menuItems);
    }

    private function getTransformer(): TransformerAbstract
    {
        return new MenuItemTransformer();
    }

    private function getManager(): Manager
    {
        return new Manager();
    }

    /**
     * Returns all child nav_menu_items under a specific parent
     *
     * @param      $parentId
     * @param      $navMenuItems
     * @param bool $depth Whether to include multilevel children
     *
     * @return \Illuminate\Support\Collection returns filtered array of nav_menu_items
     */
    private function getNavMenuItemChildren($parentId, $navMenuItems, $depth = true): \Illuminate\Support\Collection
    {
        return collect($navMenuItems)
            ->reduce(function ($children, $navMenuItem) use ($navMenuItems, $depth, $parentId) {
                if ($navMenuItem->menu_item_parent == $parentId) {
                    $children->push($navMenuItem);
                    if ($depth) {
                        if ($subChildren = $this->getNavMenuItemChildren($navMenuItem->ID, $navMenuItems)) {
                            $children->merge($subChildren);
                        }
                    }
                }
                return $children;
            }, collect([]));
    }

    private function getMenuItems($menuId)
    {
        // Returns all menu items without structure
        $rawMenuItems = wp_get_nav_menu_items($menuId);

        // We loop to format the menu items with children
        $formattedMenuItems = collect($rawMenuItems)->map(function ($rawMenuItem) use ($rawMenuItems) {
            // Skip children as they are found when looping parents
            if (!$rawMenuItem || $rawMenuItem->menu_item_parent != 0) {
                return null;
            }
            // Set the children
            $rawMenuItem->children = $this->getNavMenuItemChildren($rawMenuItem->ID, $rawMenuItems);
            // Wrap in adapter
            return new MenuItem(new MenuItemAdapter($rawMenuItem));
        })->reject(function ($menuItem) {
            return is_null($menuItem);
        })->toArray();

        return $this->getManager()
            ->createData(new Collection($formattedMenuItems, $this->getTransformer()))
            ->toArray();
    }
}
