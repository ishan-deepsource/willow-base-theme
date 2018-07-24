<?php

namespace Bonnier\Willow\Base\Helpers;

class SocialMediaHelper
{
    public static function facebookMetaTags()
    {
        add_action('wp_head', function () {
            $page_ids = str_replace("\n", ",", get_field('facebook_page_ids', 'option'));
            if (strlen($page_ids) > 0) {
                echo '<meta property="fb:pages" content="' . $page_ids . '"></meta>' . PHP_EOL;
            }

            if ($facebook_id = wpSiteManager()->sites()->findById(wpSiteManager()->settings()->getSiteId(get_locale()))->facebook_id) {
                echo '<meta property="fb:app_id" content="' . $facebook_id . '" />' . PHP_EOL;
            }
        });
    }

    public static function facebookScript()
    {
        add_action('body_start', function () {
            if ($facebook_id = wpSiteManager()->sites()->findById(wpSiteManager()->settings()->getSiteId(get_locale()))->facebook_id) {
                echo '<div id="fb-root"></div>
                <script>(function(d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) return;
                    js = d.createElement(s); js.id = id;
                    js.src = "//connect.facebook.net/da_DK/sdk.js#xfbml=1&version=v2.10&appId=' . $facebook_id . '";
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, \'script\', \'facebook-jssdk\'));</script>';
            }
        });
    }
}
