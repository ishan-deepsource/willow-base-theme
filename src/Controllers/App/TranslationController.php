<?php

namespace Bonnier\Willow\Base\Controllers\App;

use Bonnier\Willow\Base\Helpers\Cache;
use Bonnier\Willow\Base\Repositories\TranslationManagerRepository;
use Bonnier\Willow\Base\Repositories\TranslationRepositoryContract;
use WP_REST_Controller;
use WP_REST_Response;
use WpSiteManager\Plugin;

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
     * @return WP_REST_Response
     */
    public function translationStrings(\WP_REST_Request $request)
    {
        $locale = $request->get_param('locale') ?? pll_current_language();
        
        $translations = Cache::remember(
            'willow_translation_strings_' . $locale,
            4 * HOUR_IN_SECONDS,
            function () use ($locale) {
                return $this->getRepository()->getTranslations($locale) ?? [];
            }
        );
        
        return new WP_REST_Response($translations);
    }
    
    /**
     * @return TranslationRepositoryContract|null
     */
    private function getRepository() : ?TranslationRepositoryContract
    {
        if ($site = Plugin::instance()->settings()->getSite(pll_current_language('locale'))) {
            $translationManagerHost = getenv('TRANSLATION_MANAGER_HOST');
            $serviceId = getenv('SERVICE_ID');
            return new TranslationManagerRepository(
                $translationManagerHost,
                $serviceId,
                $site->brand->id
            );
        }
        
        return null;
    }
}
