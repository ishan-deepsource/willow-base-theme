<?php

namespace Bonnier\Willow\Base\Transformers\Api\Search;

use Bonnier\Willow\Base\Models\Contracts\Search\FacetCollectionContract;
use Bonnier\Willow\Base\Models\Contracts\Search\FacetContract;
use League\Fractal\TransformerAbstract;

/**
 * Class FacetTransformer
 *
 * @package \Bonnier\Willow\Base\Transformers\Api\Composites\Partials
 */
class FacetCollectionTransformer extends TransformerAbstract
{
    public function transform(FacetCollectionContract $facetCollection)
    {
        return [
            'label' => $facetCollection->getLabel(),
            'field' => $facetCollection->getField(),
            'data' => $this->getFacets($facetCollection)
        ];
    }

    private function getFacets(FacetCollectionContract $facetCollection)
    {
        return $facetCollection->getFacets()->map(function (FacetContract $facetContract) {
            return with(new FacetTransformer())->transform($facetContract);
        });
    }
}
