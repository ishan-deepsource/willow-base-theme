<?php

namespace Bonnier\Willow\Base\Models\Contracts\Search;

use Illuminate\Support\Collection;

interface FacetCollectionContract
{
    public function getLabel(): string;
    public function getField(): string;
    public function getFacets(): Collection;
}
