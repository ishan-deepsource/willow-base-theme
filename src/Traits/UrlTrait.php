<?php

namespace Bonnier\Willow\Base\Traits;

use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

trait UrlTrait
{
    protected function getPath($url)
    {
        $hosts = [
            parse_url(home_url(), PHP_URL_HOST),
            $_SERVER['HTTP_HOST'],
        ];
        $hosts[] = parse_url(LanguageProvider::getHomeUrl(), PHP_URL_HOST);
        if ($url && in_array(parse_url($url, PHP_URL_HOST), $hosts)) {
            $path = parse_url($url, PHP_URL_PATH);
            if ($query = parse_url($url, PHP_URL_QUERY)) {
                return $path . '?' . $query;
            }
            return $path;
        }

        return $url;
    }

    protected function getFullUrl($url, ?string $locale = null)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $url = preg_replace('#/$#', $path, LanguageProvider::getHomeUrl('', $locale));
        return $this->stripApi($url);
    }

    protected function stripApi($url)
    {
        return preg_replace('#://api\.#', '://', $url);
    }
}
