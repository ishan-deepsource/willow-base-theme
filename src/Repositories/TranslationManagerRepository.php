<?php

namespace Bonnier\Willow\Base\Repositories;

use Bonnier\Willow\Base\Services\TranslationManagerService;

class TranslationManagerRepository implements TranslationRepositoryContract
{
    private $translationService;

    /**
     * TranslationManagerRepository constructor.
     * @param $translationManagerHost
     * @param $serviceId
     * @param $brandId
     */
    public function __construct($translationManagerHost, $serviceId, $brandId)
    {
        $this->translationService = new TranslationManagerService($translationManagerHost, $serviceId, $brandId);
    }

    /**
     * @param string|null $locale
     * @return array|null
     */
    public function getTranslations(string $locale = null): ?array
    {
        if ($translationsResponse = $this->translationService->getTranslations($locale)) {
            $translations = [];

            foreach ($translationsResponse as $trans) {
                foreach ($trans->value as $lang => $val) {
                    $this->explodeNestedArray($translations, $trans->key, $val, $lang);
                }
            }

            if ($locale) {
                return $translations[$locale] ?? $translations['en'] ?? null;
            }
            return $translations;
        }

        return null;
    }

    /**
     * @param $translations
     * @param $path
     * @param $value
     * @param $lang
     * @param string $separator
     */
    public function explodeNestedArray(&$translations, $path, $value, $lang)
    {
        data_set($translations, $lang . '.' .$path, $value);
    }
}
