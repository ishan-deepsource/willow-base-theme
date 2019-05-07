<?php
namespace Bonnier\Willow\Base\Actions\Backend;

use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

class Locale
{
    public function __construct()
    {
        add_filter('locale', [$this, 'getLocale'], 1000);
    }

    public function getLocale($locale) {
        return collect(LanguageProvider::getSimpleLanguageList(['fields' => 'locale']))
         ->first(function ($locale) {
             return str_contains($locale, LanguageProvider::getCurrentLanguage());
         }, $locale);
    }
}