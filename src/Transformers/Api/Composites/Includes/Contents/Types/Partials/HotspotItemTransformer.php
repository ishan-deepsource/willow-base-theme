<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\HotspotItemContract;
use League\Fractal\TransformerAbstract;

class HotspotItemTransformer extends TransformerAbstract
{
    public function transform(HotspotItemContract $hotspot)
    {
        return [
            'title' => $hotspot->getTitle(),
            'description' => $hotspot->getDescription(),
            'coordinates' => $hotspot->getCoordinates(),
        ];
    }
}
