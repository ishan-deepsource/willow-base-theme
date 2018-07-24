<?php

namespace Bonnier\Willow\Base\Traits;

trait UrlTrait
{
    protected function getPath($url)
    {
        $hosts = [
            parse_url(home_url(), PHP_URL_HOST),
            $_SERVER['HTTP_HOST'],
        ];
        if (function_exists('pll_home_url')) {
            $hosts[] = parse_url(pll_home_url(), PHP_URL_HOST);
        }
        if ($url && in_array(parse_url($url, PHP_URL_HOST), $hosts)) {
            $path = parse_url($url, PHP_URL_PATH);
            if ($query = parse_url($url, PHP_URL_QUERY)) {
                return $path . '?' . $query;
            }
            return $path;
        }

        return $url;
    }

    protected function getFullUrl($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (function_exists('pll_home_url')) {
            $url = preg_replace('#/$#', $path, pll_home_url());
        }
        return preg_replace('#://api\.#', '://', $url);
    }
}
