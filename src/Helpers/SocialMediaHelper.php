<?php

namespace Bonnier\Willow\Base\Helpers;

use Bonnier\WP\SiteManager\WpSiteManager;

class SocialMediaHelper
{
    public static function facebookMetaTags()
    {
        add_action('wp_head', function () {
            $pageIds = str_replace("\n", ",", get_field('facebook_page_ids', 'option'));
            if (strlen($pageIds) > 0) {
                echo '<meta property="fb:pages" content="' . $pageIds . '"></meta>' . PHP_EOL;
            }

            if ($facebookId = WpSiteManager::instance()->sites()->findById(
                WpSiteManager::instance()->settings()->getSiteId(get_locale())
            )->facebook_id) {
                echo '<meta property="fb:app_id" content="' . $facebookId . '" />' . PHP_EOL;
            }
        });
    }

    public static function facebookScript()
    {
        add_action('body_start', function () {
            if ($facebookId = WpSiteManager::instance()->sites()->findById(
                WpSiteManager::instance()->settings()->getSiteId(get_locale())
            )->facebook_id) {
                echo '<div id="fb-root"></div>
                <script>(function(d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) return;
                    js = d.createElement(s); js.id = id;
                    js.src = "//connect.facebook.net/da_DK/sdk.js#xfbml=1&version=v2.10&appId=' . $facebookId . '";
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, \'script\', \'facebook-jssdk\'));</script>';
            }
        });
    }
}
