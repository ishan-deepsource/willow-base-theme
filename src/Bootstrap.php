<?php

namespace Bonnier\Willow\Base;

use Bonnier\Willow\Base\Actions\ActionsBootstrap;
use Bonnier\Willow\Base\Commands\CommandBootstrap;
use Bonnier\Willow\Base\Controllers\App\AppControllerBootstrap;
use Bonnier\Willow\Base\Controllers\Formatters\ControllerBootstrap;
use Bonnier\Willow\Base\Database\DB;
use Bonnier\Willow\Base\Database\Migrations\Migrate;
use Bonnier\Willow\Base\Repositories\NotFoundRepository;
use Bonnier\Willow\Base\Controllers\Admin\NotFoundController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Bootstrap
 *
 * @package \Bonnier\Willow\Base
 */
class Bootstrap
{
    /** @var NotFoundController */
    private static $notFoundController;
    
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
        add_action('load-' . $pageHook, [Bootstrap::class, 'loadNotFoundListScreenOptions']);
    }

    public static function loadNotFoundListTable()
    {
        self::$notFoundController->displayNotFoundTable();
    }

    public static function loadNotFoundListScreenOptions()
    {
        $database = new DB();
        $notFoundRepository = new NotFoundRepository($database);
        $request = Request::createFromGlobals();
        self::$notFoundController = new NotFoundController($notFoundRepository, $request);
    }
}
