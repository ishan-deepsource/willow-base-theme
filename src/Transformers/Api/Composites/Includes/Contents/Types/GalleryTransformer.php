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
            'display_hint' => $gallery->getDisplayHint(),
            'images' => $gallery->isLocked() ?
                null :
                $gallery->getImages()->map(function (GalleryImageContract $galleryImage) {
                    return with(new GalleryImageTransformer())->transform($galleryImage);
                }),
        ];
    }
}
