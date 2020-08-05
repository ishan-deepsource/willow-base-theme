<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;
use League\Fractal\TransformerAbstract;

class TeaserTransformer extends TransformerAbstract
{
    public function transform(TeaserContract $teaser)
    {
        return [
            'title' => $teaser->getTitle(),
            'image' => $this->transformImage($teaser),
            'video_url' => $teaser->getVideoUrl(),
            'description' => $teaser->getDescription(),
            'type' => $teaser->getType()
        ];
    }

    private function transformImage(TeaserContract $teaser)
    {
        if ($image = $teaser->getImage()) {
            return with(new ImageTransformer())->transform($image);
        }

        return null;
    }
}
