<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\HotspotImageContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\HotspotItemContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\Partials\HotspotItemTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use League\Fractal\TransformerAbstract;

class HotspotImageTransformer extends TransformerAbstract
{
    public function transform(HotspotImageContract $image)
    {
        return [
            'title'        => $image->getTitle(),
            'description'  => $image->getDescription(),
            'image'        => $this->getImage($image),
            'display_hint' => $image->getDisplayHint(),
            'hotspots'     => $this->getHotspots($image),
        ];
    }

    private function getHotspots(HotspotImageContract $image)
    {
        return $image->getHotspots()->map(function (HotspotItemContract $hotspot) {
            return with(new HotspotItemTransformer())->transform($hotspot);
        });
    }

    private function getImage(HotspotImageContract $hotspotImage)
    {
        if ($image = $hotspotImage->getImage()) {
            return with(new ImageTransformer())->transform($image);
        }
        return null;
    }
}
