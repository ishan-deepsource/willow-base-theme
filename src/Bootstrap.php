<?php

namespace Bonnier\Willow\Base;

use Bonnier\Willow\Base\Actions\ActionsBootstrap;
use Bonnier\Willow\Base\Commands\CommandBootstrap;
use Bonnier\Willow\Base\Controllers\Admin\NotFoundSettingsController;
use Bonnier\Willow\Base\Controllers\App\AppControllerBootstrap;
use Bonnier\Willow\Base\Controllers\Formatters\ControllerBootstrap;
use Bonnier\Willow\Base\Database\DB;
use Bonnier\Willow\Base\Database\Migrations\Migrate;
use Bonnier\Willow\Base\Repositories\NotFoundRepository;
use Bonnier\Willow\Base\Controllers\Admin\NotFoundListController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Bootstrap
 *
 * @package \Bonnier\Willow\Base
 */
class Bootstrap
{
    /** @var NotFoundListController */
    private static $notFoundListController;
    /** @var NotFoundSettingsController */
    private static $notFoundSettingsController;

    /**
     * Boostrap constructor.
     */
    public function __construct()
    {
        Migrate::run();

        new ControllerBootstrap();
        new CommandBootstrap();
        new ActionsBootstrap();
        new AppControllerBootstrap();
    }

    public static function loadAdminMenu()
    {
        $pageHook = add_menu_page(
            'Not Found',
            'Not Found',
            'manage_categories', // Editor role
            'not-found',
            [Bootstrap::class, 'loadNotFoundListTable'],
            'dashicons-editor-unlink'
        );
        $settingsHook = add_submenu_page(
            'not-found',
            'Settings',
            'Settings',
            'manage_options',
            'not-found-settings',
            [Bootstrap::class, 'displayNotFoundSettingsPage']
        );
        add_action('load-' . $pageHook, [Bootstrap::class, 'loadNotFoundListScreenOptions']);
        add_action('load-' . $settingsHook, [Bootstrap::class, 'loadNotFoundSettingsPage']);
    }

    public static function loadNotFoundListTable()
    {
        self::$notFoundListController->displayNotFoundTable();
    }

    public static function loadNotFoundListScreenOptions()
    {
        $database = new DB();
        $notFoundRepository = new NotFoundRepository($database);
        $request = Request::createFromGlobals();
        self::$notFoundListController = new NotFoundListController($notFoundRepository, $request);
    }

    public static function displayNotFoundSettingsPage()
    {
        self::$notFoundSettingsController->displaySettingsPage();
    }

    public static function loadNotFoundSettingsPage()
    {
        $request = Request::createFromGlobals();
        self::$notFoundSettingsController = new NotFoundSettingsController($request);
        self::$notFoundSettingsController->handlePost();
    }
}
