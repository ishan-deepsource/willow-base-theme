<?php

namespace Bonnier\Willow\Base\Actions\Backend;

use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\ContentHub\Editor\Models\WpComposite;
use Bonnier\WP\Redirect\Http\BonnierRedirect;
/**
 * Class PostSlugChange
 *
 * @package \Bonnier\Willow\Base\Actions\Backend
 */
class PostSlugChange
{
    public function __construct()
    {
        add_filter(WpComposite::SLUG_CHANGE_HOOK, [$this, 'createRedirectOnSlugChange'], 10, 3);
    }

    public function createRedirectOnSlugChange($postId, $oldLink, $newLink) {
        //dd($oldLink, $newLink);
        BonnierRedirect::createRedirect(
            parse_url($oldLink, PHP_URL_PATH),
            parse_url($newLink, PHP_URL_PATH),
            LanguageProvider::getCurrentLanguage(),
            'post-slug-change',
            $postId
        );
    }
}

