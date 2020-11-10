<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\MultimediaContract;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use League\Fractal\TransformerAbstract;

class MultimediaTransformer extends TransformerAbstract
{
    public function transform(MultimediaContract $multimedia)
    {
        return [
            'title' => $multimedia->isLocked() ? null : $multimedia->getTitle(),
            'description' => $multimedia->isLocked() ? null : $multimedia->getDescription(),
            'image' => $multimedia->isLocked() ? null : $this->transformImage($multimedia),
            'display_hint' => $multimedia->isLocked() ? null : $multimedia->getDisplayHint(),
            'vectary_id' => $multimedia->isLocked() ? null : $multimedia->getVectaryId(),
            'vectary_url' => $multimedia->isLocked() ? null : $multimedia->getVectaryUrl(),
        ];
    }

    private function transformImage(MultimediaContract $multimedia)
    {
        if ($image = $multimedia->getImage()) {
            return with(new ImageTransformer)->transform($image);
        }

        return null;
    }
}
