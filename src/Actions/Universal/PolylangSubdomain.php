<?php

namespace Bonnier\Willow\Base\Actions\Universal;


class PolylangSubdomain
{
    public function __construct()
    {
        add_filter('option_polylang', [$this, 'registerSubdomain']);
    }

    public function registerSubdomain($options)
    {
        foreach ($options['domains'] as $locale => $domain) {
            if (str_contains($_SERVER['HTTP_HOST'], parse_url($domain, PHP_URL_HOST))) {
                $subDomain = sprintf('%s://%s', parse_url($domain, PHP_URL_SCHEME), $_SERVER['HTTP_HOST']);
                $options['domains'][$locale] = $subDomain;
            }
        }
        return $options;
    }
}
