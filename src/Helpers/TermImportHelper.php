<?php

namespace Bonnier\Willow\Base\Helpers;

use Bonnier\Willow\Base\Commands\Taxonomy\Helpers\WpTerm;
use Bonnier\Willow\Base\Models\WpTaxonomy;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Exception;

class TermImportHelper
{
    protected $taxonomy;
    protected $currentLocale;

    /**
     * TermImportHelper constructor.
     * @param $taxonomy
     * @throws Exception
     */
    public function __construct($taxonomy)
    {
        $this->taxonomy = $this->getWpTaxonomy($taxonomy);
    }

    public function importTermAndLinkTranslations($externalTerm)
    {
        $termIdsByLocale = collect($externalTerm->name)->map(function ($name, $languageCode) use ($externalTerm) {
            if (!collect(LanguageProvider::getSimpleLanguageList())->contains($languageCode)) {
                return null;
            }
            return [$languageCode, $this->importTerm($name, $languageCode, $externalTerm)];
            // Creates an associative array with language code as key and term id as value
        })->toAssoc()->rejectNullValues()->toArray();
        LanguageProvider::saveTermTranslations($termIdsByLocale);
        return $termIdsByLocale;
    }

    public function deleteTerm(\WP_Term $term)
    {
        $result = wp_delete_term($term->term_id, $this->taxonomy);
        if (! is_wp_error($result)) {
            return true;
        }
        return false;
    }

    protected function importTerm($name, $languageCode, $externalTerm)
    {
        $contentHubId = $externalTerm->content_hub_ids->{$languageCode};
        $parentTermId = $this->getParentTermId($languageCode, $externalTerm->parent ?? null);
        $taxonomy = isset($externalTerm->vocabulary) ?
            WpTaxonomy::get_taxonomy($externalTerm->vocabulary->content_hub_id) :
            $this->taxonomy;
        $this->setLocaleFilter($languageCode);
        $description = $externalTerm->description->{$languageCode} ?? null;
        $slug = object_get($externalTerm, 'slug.'.$languageCode) ?: $name;

        $meta = [
            'meta_title' => $externalTerm->meta_title->{$languageCode} ?? null,
            'meta_description' => $externalTerm->meta_description->{$languageCode} ?? null,
            'body' => $externalTerm->body->{$languageCode} ?? null,
            'image_url' => $externalTerm->image_url->{$languageCode} ?? null,
            'internal' => $externalTerm->internal ?? false,
            'whitealbum_id' => $externalTerm->whitealbum_id->{$languageCode} ?? null,
        ];

        if ($existingTermId = WpTerm::id_from_contenthub_id($contentHubId)) {
            // Term exists so we update it
            return WpTerm::update(
                $existingTermId,
                $name,
                $slug,
                $languageCode,
                $contentHubId,
                $taxonomy,
                $parentTermId,
                $description,
                $meta
            );
        }
        // Create new term
        return WpTerm::create(
            $name,
            $slug,
            $languageCode,
            $contentHubId,
            $taxonomy,
            $parentTermId,
            $description,
            $meta
        );
    }

    protected function getParentTermId($languageCode, $externalCategory)
    {
        if (! isset($externalCategory->name->{$languageCode})) {
            // Make sure we only create the parent term if a translation exists for the language of the child term
            return null;
        }
        if ($existingTermId = WpTerm::id_from_contenthub_id($externalCategory->content_hub_ids->{$languageCode})) {
            // Term already exists so no need to create it again
            return $existingTermId;
        }
        return $this->importTermAndLinkTranslations($externalCategory)[$languageCode] ?? null;
    }

    public function deleteTermAndTranslations($externalTerm)
    {
        collect($externalTerm->content_hub_ids)->each(function ($contentHubId) {
            if ($wpTermId = WpTerm::id_from_contenthub_id($contentHubId) ?? null) {
                $wpTerm = get_term($wpTermId);
                if ($wpTerm instanceof \WP_Term) {
                    $this->deleteTerm($wpTerm);
                }
            }
        });
    }

    /**
     * @param $taxonomy
     * @return mixed
     * @throws Exception
     */
    protected function getWpTaxonomy($taxonomy)
    {
        $wpTaxonomy = collect([
            'category' => 'category',
            'tag'      => 'post_tag',
            'post_tag' => 'post_tag'
        ])
            ->merge(collect(WpTaxonomy::get_custom_taxonomies()->pluck('machine_name')->keyBy(function ($value) {
                return $value;
            })))
            ->get($taxonomy);

        if (! $wpTaxonomy) {
            throw new Exception(sprintf('Unsupported taxonomy: %s', $taxonomy));
        }
        return $wpTaxonomy;
    }

    private function setLocaleFilter($languageCode)
    {
        // Needed by Polylang to allow same term name in different languages
        $_POST['term_lang_choice'] = $languageCode;
        $this->currentLocale = collect(LanguageProvider::getSimpleLanguageList(['fields' => 'locale']))
            ->first(function ($locale) use ($languageCode) {
                return str_contains($locale, $languageCode);
            });
        // Needed by wordpress in order to generate the correct slug
        add_filter('locale', [$this, 'getLocaleForSanitizeTitle'], 100);
    }

    public function getLocaleForSanitizeTitle($locale)
    {
        return $this->currentLocale ?: $locale;
    }
}
