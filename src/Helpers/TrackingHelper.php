<?php

namespace Bonnier\Willow\Base\Helpers;

class TrackingHelper
{
    public static function tnsScripts()
    {
        switch (get_locale()) {
            case "da_DK":
                if (!self::tnsSettings('da_DK')) {
                    break;
                }

                $scriptSrc = get_template_directory_uri() . '/assets/scripts/spring.js';
                self::addRawScript("<script>
                    var springq = springq || [];
                    window.springq.push({
                        s: \"" . self::tnsSettings('da_DK')->sitename . "\",
                        cp: \"" . self::tnsSettings('da_DK')->contentpath . "\" + window.location.pathname,
                        url: window.location.href
                    });
                </script>", false);

                self::addRawScript("<script> (function() {
                    var scr = document.createElement('script');
                    scr.type = 'text/javascript';
                    scr.async = true;
                    scr.src = '$scriptSrc';
                    var s = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(scr, s);
                })();
                </script>", true);

                self::addRawScript("<noscript><img src=\"http://" . self::tnsSettings('da_DK')->sitename . ".tns-gallup.dk/j0=,,,;+,cp=" . self::tnsSettings('da_DK')->contentpath . "+url=" . pll_home_url(pll_current_language('slug')) . ";;;\" alt=\"\"></noscript>", true);
                break;

            case "nb_NO":
                if (!self::tnsSettings('nb_NO')) {
                    break;
                }

                $scriptSrc = get_template_directory_uri() . '/assets/scripts/unispring.js';
                self::addRawScript(
                    "<script type=\"text/javascript\">
                var measurement = {
                     's': '" . self::tnsSettings('nb_NO')->sitename . "',
                     'cp':'" . self::tnsSettings('nb_NO')->contentpath . "' + window.location.pathname,
                     'url': window.location.toString()
                };

                function loadUnispringTNS() {
                    if(typeof window.unispring === 'undefined') {
                        window.setTimeout(loadUnispringTNS, 1000);
                    } else {
                        window.unispring.c(measurement);
                    }
                }
                loadUnispringTNS();
                </script>", false);

                self::addRawScript("<script> (function() {
                    var scr = document.createElement('script');
                    scr.type = 'text/javascript';
                    scr.async = true;
                    scr.src = '$scriptSrc';
                    var s = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(scr, s);
                })();
                </script>", true);

                self::addRawScript("<noscript><img src=\"http://" . self::tnsSettings('nb_NO')->sitename . ".tns-cs.net/j0=,,,;+,cp=" . self::tnsSettings('nb_NO')->contentpath . "+url=" . pll_home_url(pll_current_language('slug')) . ";;;\"></noscript>",
                    false);
                break;

            case "fi":
                if (!self::tnsSettings('fi')) {
                    break;
                }

                $scriptSrc = get_template_directory_uri() . '/assets/scripts/spring_fi.js';
                self::addRawScript("<script>
                    var springq = springq || [];
                    window.springq.push({
                        s: \"" . self::tnsSettings('fi')->sitename . "\",
                        cp: \"" . self::tnsSettings('fi')->contentpath . "\" + window.location.pathname,
                        url: window.location.href
                    });
                </script>", false);

                self::addRawScript("<script> (function() {
                    var scr = document.createElement('script');
                    scr.type = 'text/javascript';
                    scr.async = true;
                    scr.src = '$scriptSrc';
                    var s = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(scr, s);
                })();
                </script>", true);

                self::addRawScript("<noscript><img src=\"http://" . self::tnsSettings('fi')->sitename . ".spring-tns.net/j0=,,,;+,,cp=" . self::tnsSettings('fi')->contentpath . "+url=" . pll_home_url(pll_current_language('slug')) . ";;;\" alt=\"\"></noscript>",
                    false);
                break;

            default:
                break;
        }
    }

    public static function addRawScript($raw, $footer = true)
    {
        $hook = $footer ? 'wp_footer' : 'wp_head';
        add_action($hook, function () use ($raw) {
            echo $raw;
        });
    }

    public static function addScript($handle, $src, $footer = false)
    {
        add_action('wp_enqueue_scripts', function () use ($handle, $src, $footer) {
            wp_enqueue_script($handle, $src, false, null, $footer);
        });
    }

    /**
     * TNS settings from Theme Settings
     * @param string $locale
     * @return \stdClass
     */
    public static function tnsSettings($locale)
    {
        $lines = explode("\n", strtolower(get_field('tns_settings', 'option')));
        foreach ($lines as $line) {
            $values = explode(',', $line);
            if (trim($values[0]) == strtolower($locale)) {
                $obj = new \stdClass();
                $obj->sitename = trim($values[1]) ?? '';
                $obj->contentpath = trim($values[2] ?? '');
                return $obj;
            }
        }
        return false;
    }
}
