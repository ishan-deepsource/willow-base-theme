<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\BrandContract;
use League\Fractal\TransformerAbstract;

class BrandTransformer extends TransformerAbstract
{
    public function transform(BrandContract $brand)
    {
        return [
            'id' => $brand->getId(),
            'name' => $brand->getName(),
            'brand_code' => $brand->getBrandCode(),
        ];
    }
}
