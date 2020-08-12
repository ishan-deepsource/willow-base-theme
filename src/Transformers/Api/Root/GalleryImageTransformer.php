<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\GalleryImageContract;
use League\Fractal\TransformerAbstract;

class GalleryImageTransformer extends TransformerAbstract
{
    public function transform(GalleryImageContract $galleryImage)
    {
        return [
            'title' => $galleryImage->getTitle(),
            'description' => $galleryImage->getDescription(),
            'image' => with(new ImageTransformer)->transform($galleryImage->getImage()),
            'video_url' => $galleryImage->getVideoUrl(),
        ];
    }
}
