<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\GalleryContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
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
            'images' => $gallery->isLocked() ? null : $gallery->getImages()->map(function (ImageContract $image) {
                return with(new ImageTransformer)->transform($image);
            }),
        ];
    }
}
