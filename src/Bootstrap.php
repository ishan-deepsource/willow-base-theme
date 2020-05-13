<?php

namespace Bonnier\Willow\Base;

use Bonnier\Willow\Base\Actions\ActionsBootstrap;
use Bonnier\Willow\Base\Commands\CommandBootstrap;
use Bonnier\Willow\Base\Controllers\Admin\NotFoundSettingsController;
use Bonnier\Willow\Base\Controllers\App\AppControllerBootstrap;
use Bonnier\Willow\Base\Controllers\Formatters\ControllerBootstrap;
use Bonnier\Willow\Base\Controllers\Root\PageController;
use Bonnier\Willow\Base\Database\DB;
use Bonnier\Willow\Base\Database\Migrations\Migrate;
use Bonnier\Willow\Base\Repositories\NotFoundRepository;
use Bonnier\Willow\Base\Controllers\Admin\NotFoundListController;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\WP\Redirect\Models\Redirect;
use Bonnier\WP\Redirect\WpBonnierRedirect;
use Bonnier\WP\SiteManager\WpSiteManager;
use Bonnier\WP\Sitemap\WpBonnierSitemap;
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
        add_action(WpBonnierRedirect::ACTION_REDIRECT_SAVED, [Bootstrap::class, 'removeNotFoundRedirects']);
        add_filter(WpBonnierSitemap::FILTER_ALLOW_USER_IN_SITEMAP, [Bootstrap::class, 'allowUserInSitemap'], 10, 2);
        add_filter(WpBonnierSitemap::FILTER_TAG_ALLOWED_IN_SITEMAP, [Bootstrap::class, 'allowTagInSitemap'], 10, 2);

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
        $notFoundRepository = new NotFoundRepository(new DB());
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

    public static function registerPageRestController(array $args, string $postType)
    {
        if ($postType === 'page') {
            $args['rest_controller_class'] = PageController::class;
        }
        return $args;
    }

    public static function removeNotFoundRedirects(Redirect $redirect)
    {
        $notFoundRepository = new NotFoundRepository(new DB());
        $notFoundRepository->deleteByUrlAndLocale($redirect->getFrom(), $redirect->getLocale());
    }

    public static function allowUserInSitemap(bool $allowInSitemap, int $userID)
    {
        return boolval(get_user_meta($userID, 'public', true));
    }
    
    public static function allowTagInSitemap(bool $allowed, \WP_Term $tag)
    {
        if (get_term_meta($tag->term_id, 'internal', true)) {
            return false;
        }
        return true;
    }
}
