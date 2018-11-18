<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\GalleryContract;
use Bonnier\Willow\Base\Models\Contracts\Root\GalleryImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Transformers\Api\Root\GalleryImageTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use League\Fractal\TransformerAbstract;

/**
 * Class VideoTransformer
 *
 * @package \Bonnier\Willow\Base\Transformers\Api\Composites\Partials
 */
class GalleryTransformer extends TransformerAbstract
{
    public function transform(GalleryContract $gallery)
    {
        return [
            'title' => $gallery->getTitle(),
            'description' => $gallery->getDescription(),
            'display_hint' => $gallery->getDisplayHint(),
            'images' => $this->getImages($gallery),
        ];
    }

    private function getImages(GalleryContract $gallery)
    {
        if ($gallery->isLocked() || $gallery->getImages()->isEmpty()) {
            return null;
        }

        return $gallery->getImages()->map(function (GalleryImageContract $galleryImage) {
            if ($galleryImage->getImage() && $galleryImage->getImage()->getUrl()) {
                return with(new GalleryImageTransformer())->transform($galleryImage);
            }
            return null;
        })->reject(function ($galleryImage) {
            return is_null($galleryImage);
        })->values(); // Return values to make sure that images are encoded as array and not object
    }
}
