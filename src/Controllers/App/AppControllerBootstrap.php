<?php

namespace Bonnier\Willow\Base\Controllers\App;

use Bonnier\Willow\Base\Exceptions\Controllers\Api\MissingRestControllerException;
use WP_REST_Controller;

/**
 * Class ControllerBootstrap
 *
 * @package \Bonnier\Willow\Base\Controllers\Formatters
 */
class AppControllerBootstrap
{
    protected $apiControllers = array(
        RouteController::class,
        TranslationController::class,
        MenuController::class,
        SocialFeedController::class,
        SearchController::class,
        PermissionTextTest::class,
        TestController::class,
        SitemapController::class,
        SettingsController::class,
    );

    /**
     * ControllerBootstrap constructor.
     */
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'registerApiControllers']);
    }

    public function registerApiControllers()
    {
        collect($this->apiControllers)->each(function ($controllerClass) {
            /** @var WP_REST_Controller $controller */
            $controller = new $controllerClass();
            if ($controller instanceof WP_REST_Controller) {
                $controller->register_routes();
            } else {
                throw new MissingRestControllerException(
                    sprintf('\'%s\' must extend WP_REST_Controller', $controllerClass)
                );
            }
        });
    }
}
