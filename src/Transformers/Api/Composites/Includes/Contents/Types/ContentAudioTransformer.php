<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentAudioContract;
use Bonnier\Willow\Base\Transformers\Api\Root\AudioTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use League\Fractal\TransformerAbstract;

class ContentAudioTransformer extends TransformerAbstract
{
    public function transform(ContentAudioContract $audio)
    {
        return [
            'title' => $audio->getAudioTitle(),
            'file' => $audio->isLocked() ? null : with(new AudioTransformer())->transform($audio),
            'image' => with(new ImageTransformer())->transform($audio->getImage())
        ];
    }
}
