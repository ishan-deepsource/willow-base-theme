<?php

namespace Bonnier\Willow\Base\Controllers\App;

use Bonnier\Willow\Base\Helpers\Cache;
use Bonnier\Willow\Base\Repositories\TranslationManagerRepository;
use Bonnier\Willow\Base\Repositories\TranslationRepositoryContract;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\SiteManager\WpSiteManager;
use WP_REST_Controller;
use WP_REST_Response;

class TranslationController extends WP_REST_Controller
{
    public function register_routes()
    {
        register_rest_route('app', '/translation-strings', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, 'translationStrings']
        ]);
    }

    /**
     * @param \WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function translationStrings(\WP_REST_Request $request)
    {
        $locale = $request->get_param('lang');

        $translations = Cache::remember(
            'willow_translation_strings_' . $locale,
            4 * HOUR_IN_SECONDS,
            function () use ($locale) {
                return optional($this->getRepository())->getTranslations($locale) ?? [];
            }
        );

        return new WP_REST_Response($translations);
    }

    /**
     * @return TranslationRepositoryContract|null
     */
    private function getRepository() : ?TranslationRepositoryContract
    {
        if ($site = WpSiteManager::instance()->settings()->getSite(LanguageProvider::getCurrentLanguage('locale'))) {
            $tmHost = env('TRANSLATION_MANAGER_HOST');
            $serviceId = env('SERVICE_ID');
            if ($tmHost && $serviceId) {
                return new TranslationManagerRepository(
                    $tmHost,
                    $serviceId,
                    $site->brand->id
                );
            }
        }

        return null;
    }
}
