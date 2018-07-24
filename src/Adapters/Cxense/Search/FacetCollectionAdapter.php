<?php

namespace Bonnier\Willow\Base\Adapters\Cxense\Search;

use Bonnier\Willow\Base\Models\Contracts\Search\FacetCollectionContract;
use Illuminate\Support\Collection;

/**
 * Class CategoryAdapter
 *
 * @package \\${NAMESPACE}
 */
class FacetCollectionAdapter implements FacetCollectionContract
{
    protected $cxFacetCollection;

    public function __construct($cxFacetCollection)
    {
        $this->cxFacetCollection = $cxFacetCollection;
    }

    public function getLabel(): string
    {
        return $this->cxFacetCollection->label ?? '';
    }

    public function getField(): string
    {
        return $this->cxFacetCollection->field ?? '';
    }

    public function getFacets(): Collection
    {
        return collect($this->cxFacetCollection->buckets ?? [])->map(function ($facet) {
            return new FacetAdapter($facet);
        });
    }
}
