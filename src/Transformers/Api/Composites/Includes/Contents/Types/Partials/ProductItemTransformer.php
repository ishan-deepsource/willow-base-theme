<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\ProductItemContract;
use League\Fractal\TransformerAbstract;

class ProductItemTransformer extends TransformerAbstract
{
    public function transform(ProductItemContract $productItem)
    {
        return [
            'key' => $productItem->getParameter(),
            'value' => $productItem->getScore(),
        ];
    }
}
