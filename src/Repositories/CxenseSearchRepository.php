<?php

namespace Bonnier\Willow\Base\Repositories;

class CxenseSearchRepository
{
    /**
     * @var array Holds the response from cXense in memory
     */
    protected $queryResults = [];
    protected $orgPrefix;

    public function __construct()
    {
        $this->orgPrefix = wp_cxense()->settings->get_setting_value('organisation_prefix', get_locale());
    }

    /**
     * @param string $searchQuery
     * @param $page
     * @param int $perPage
     * @param array $customFilters
     * @param array $customSorting
     * @return array
     */
    public function getSearchResults($searchQuery = '', $page, $perPage = 10, $customFilters = [], $customSorting = [])
    {
        $searchQuery = $searchQuery ?: '*';

        if (isset($this->queryResults[$searchQuery])) {
            return $this->queryResults[$searchQuery];
        }

        $arguments = [
            'query' => $searchQuery,
            'page' => $page,
            'count' => $perPage,
            'filter_operator' => 'AND',
            'filter_exclude' => [
                $this->orgPrefix . '-pagetype' => [
                    'tag',
                ],
            ],
            'facets' => $this->getSearchableTaxonomies(),
            'highlights' => [
                0 => [
                    'field' => 'description',
                    'start' => '<strong>',
                    'stop' => '</strong>',
                    'length' => '500'
                ],
            ],
            'sorting' => [
                'type' => 'score',
                'order' => 'descending'
            ],
        ];

        if (!empty($customFilters)) {
            $arguments['filter'] = $customFilters;
        }

        if (!empty($customSorting)) {
            $arguments['sorting'] = $customSorting;
        }

        $result = wp_cxense()->search_documents($arguments);
        $result->facets = $this->formatFacets($result->facets, $arguments);
        $result->matches = $this->formatSearchResults($result->matches);

        return $this->queryResults[$searchQuery] = $result;
    }

    /**
     * @param $field
     * @return mixed|string
     */
    private function getVocabularyMachineName($field)
    {
        $rawName = static::strAfter($field, '-taxo-');
        if ($rawName === 'cat-top') {
            return 'category';
        }
        return str_replace('-', '_', $rawName);
    }

    /**
     * Return the remainder of a string after a given value.
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public static function strAfter($subject, $search)
    {
        if ($search == '') {
            return $subject;
        }

        $pos = strpos($subject, $search);

        if ($pos === false) {
            return $subject;
        }

        return substr($subject, $pos + strlen($search));
    }

    /**
     * @param string $searchQuery
     */
    public function getQueryStringFacets($searchQuery = '')
    {
        $this->getSearchResults($searchQuery, 1, 0)->facets;
    }

    /**
     * @param $facets
     * @param $searchArgs
     * @return static
     */
    private function formatFacets($facets, $searchArgs)
    {
        return collect($facets)->map(function ($facetCollection, $key) use ($searchArgs) {
            $facetCollection->field = $searchArgs['facets'][$key]['field'];
            $facetCollection->label = pll__(
                'taxonomy_' . $this->getVocabularyMachineName($searchArgs['facets'][$key]['field'])
            );
            $facetCollection->buckets = collect($facetCollection->buckets)->map(function ($facet) use ($searchArgs, $facetCollection) {
                $facet->active = collect($searchArgs['filter'][$facetCollection->field] ?? [])->contains($facet->label);
                return $facet;
            });
            return $facetCollection;
        });
    }

    /**
     * @param $results
     * @return static
     */
    private function formatSearchResults($results)
    {
        return collect($results)->map(function ($result) {
            $result->fields = collect($result->fields)->reduce(function ($fields, $fieldAndValue) {
                $fields[$fieldAndValue->field] = $fieldAndValue->value;
                return $fields;
            }, []);
            return $result;
        });
    }

    /**
     * @return array
     */
    private function getSearchableTaxonomies()
    {
        $recsTags = [];
        $searchableTaxonomies = wp_cxense()->settings->get_searchable_taxonomies(get_locale());
        foreach ($searchableTaxonomies as $taxonomy) {
            if ($taxonomy === 'category') {
                $taxonomy = 'cat-top';
            }
            $recsTags[] = [
                'type' => 'string',
                'field' => $this->orgPrefix . '-taxo-' . str_replace('_', '-', $taxonomy),
            ];
        }
        return $recsTags;
    }
}
