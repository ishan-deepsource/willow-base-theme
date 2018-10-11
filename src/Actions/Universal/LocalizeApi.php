<?php

namespace Bonnier\Willow\Base\Actions\Universal;

use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\ContentHub\Editor\Models\WpComposite;

class LocalizeApi
{
    public function __construct()
    {
        collect([
            WpComposite::POST_TYPE,
            'page',
            'post',
            'category',
            'post_tag',
        ])->each(function (string $contentType) {
            add_filter(sprintf('rest_%s_query', $contentType), [$this, 'setLanguage'], 1000, 2);
        });
    }

    public function setLanguage($args, \WP_REST_Request $request)
    {
        $args['lang'] = $request->get_param('lang') ?: LanguageProvider::getCurrentLanguage();
        return $args;
    }
}
