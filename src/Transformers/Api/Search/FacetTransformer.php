<?php

namespace Bonnier\Willow\Base\Transformers\Api\Search;

use Bonnier\Willow\Base\Models\Contracts\Search\FacetContract;
use League\Fractal\TransformerAbstract;

/**
 * Class FacetTransformer
 *
 * @package \Bonnier\Willow\Base\Transformers\Api\Composites\Partials
 */
class FacetTransformer extends TransformerAbstract
{
    public function transform(FacetContract $facet)
    {
        return [
            'label' => $facet->getLabel(),
            'count' => $facet->getCount(),
            'active' => $facet->isActive()
        ];
    }
}
