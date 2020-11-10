<?php

namespace Bonnier\Willow\Base;

use Bonnier\Willow\Base\ACF\Brands\BrandFactory;
use Bonnier\Willow\Base\ACF\ImageHotSpotCoordinates;
use Bonnier\Willow\Base\ACF\MarkdownEditor;
use Bonnier\Willow\Base\Actions\ActionsBootstrap;
use Bonnier\Willow\Base\Commands\CmdManager;
use Bonnier\Willow\Base\Commands\CommandBootstrap;
use Bonnier\Willow\Base\Controllers\Admin\NotFoundSettingsController;
use Bonnier\Willow\Base\Controllers\Api\FocalpointEndpointController;
use Bonnier\Willow\Base\Controllers\Api\UpdateEndpointController;
use Bonnier\Willow\Base\Controllers\App\AppControllerBootstrap;
use Bonnier\Willow\Base\Controllers\Formatters\ControllerBootstrap;
use Bonnier\Willow\Base\Controllers\Root\PageController;
use Bonnier\Willow\Base\Database\DB;
use Bonnier\Willow\Base\Database\Migrations\Migrate;
use Bonnier\Willow\Base\Helpers\CollectionHelper;
use Bonnier\Willow\Base\Helpers\CompositeHelper;
use Bonnier\Willow\Base\Helpers\FeatureTimeField;
use Bonnier\Willow\Base\Helpers\FocalPoint;
use Bonnier\Willow\Base\Helpers\PolylangConfig;
use Bonnier\Willow\Base\Helpers\Utils;
use Bonnier\Willow\Base\Models\WpAttachment;
use Bonnier\Willow\Base\Models\WpComposite;
use Bonnier\Willow\Base\Models\WpPage;
use Bonnier\Willow\Base\Models\WpTaxonomy;
use Bonnier\Willow\Base\Models\WpUserProfile;
use Bonnier\Willow\Base\Repositories\NotFoundRepository;
use Bonnier\Willow\Base\Controllers\Admin\NotFoundListController;
use Bonnier\Willow\Base\Repositories\SiteManager\SiteRepository;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\Redirect\Models\Redirect;
use Bonnier\WP\Redirect\WpBonnierRedirect;
use Bonnier\WP\Sitemap\WpBonnierSitemap;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Bootstrap
 *
 * @package \Bonnier\Willow\Base
 */
class Bootstrap
{
    private const FLUSH_REWRITE_RULES_FLAG = 'contenthub-editor-permalinks-rewrite-flush-rules-flag';

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

    public static function setup()
    {
        /** @var \WP_Rewrite $wp_rewrite */
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%category%/%postname%/');
        $wp_rewrite->add_permastruct('category', '/%category%');
        $wp_rewrite->add_permastruct('post_tag', '/%post_tag%');
        $wp_rewrite->use_trailing_slashes = false;
        $wp_rewrite->flush_rules();

        update_option(self::FLUSH_REWRITE_RULES_FLAG, true);
        add_action('admin_enqueue_scripts', [__CLASS__, 'loadAdminScripts']);

        CollectionHelper::register();

        $domain = Utils::removeApiSubdomain(LanguageProvider::getHomeUrl());
        $site = SiteRepository::find_by_domain(parse_url($domain, PHP_URL_HOST));
        BrandFactory::register(data_get($site, 'brand.brand_code'));
        // acf_field class is not exist in very first time wp load
        if (class_exists(\acf_field::class)) {
            new MarkdownEditor();
            new ImageHotSpotCoordinates();
            new CompositeHelper();
            FeatureTimeField::register();

            WpTaxonomy::register();
            WpPage::register();
            WpComposite::register();
            WpAttachment::register();
            WpUserProfile::register();
            CmdManager::register();
            PolylangConfig::register();

            $updateEndpoint = new UpdateEndpointController();
            $updateEndpoint->register_routes();

            FocalPoint::instance();
            $focalPointEndpoint = new FocalpointEndpointController();
            $focalPointEndpoint->register_routes();
        }
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

    public static function loadAdminScripts()
    {
        wp_register_style(
            'contenthub_editor_stylesheet',
            get_theme_file_uri('/assets/css/admin.css'),
            false,
            filemtime(get_theme_file_path('/assets/css/admin.css'))
        );
        wp_enqueue_style('contenthub_editor_stylesheet');
        WpTaxonomy::admin_enqueue_scripts();
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
