<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\ProductDetailsItemContract;
use League\Fractal\TransformerAbstract;

class ProductDetailsItemTransformer extends TransformerAbstract
{
    public function transform(ProductDetailsItemContract $productDetailsItem)
    {
        return [
            'display_hint' => $productDetailsItem->getDisplayHint(),
            'key' => $productDetailsItem->getKey(),
            'value' => $productDetailsItem->getValue(),
        ];
    }
}
