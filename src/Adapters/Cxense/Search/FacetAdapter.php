<?php

namespace Bonnier\Willow\Base\Adapters\Cxense\Search;

use Bonnier\Willow\Base\Models\Contracts\Search\FacetContract;

/**
 * Class CategoryAdapter
 *
 * @package \\${NAMESPACE}
 */
class FacetAdapter implements FacetContract
{
    protected $cxFacet;

    public function __construct($cxenseFacet)
    {
        $this->cxFacet = $cxenseFacet;
    }

    public function getLabel(): string
    {
        return $this->cxFacet->label ?? '';
    }

    public function getCount(): int
    {
        return intval($this->cxFacet->count ?? 0);
    }

    public function isActive(): bool
    {
        return boolval($this->cxFacet->active ?? false);
    }
}
