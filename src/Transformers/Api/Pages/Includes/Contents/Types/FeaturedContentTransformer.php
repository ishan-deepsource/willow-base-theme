<?php

namespace Bonnier\Willow\Base\Transformers\Api\Pages\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\FeaturedContentContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\NativeVideoTransformer;
use League\Fractal\TransformerAbstract;

class FeaturedContentTransformer extends TransformerAbstract
{
    public function transform(FeaturedContentContract $featuredContent)
    {
        return [
            'image' => $this->transformImage($featuredContent),
            'video' => $this->transformVideo($featuredContent),
            'display_hint' => $featuredContent->getDisplayHint(),
            'teaser' => $this->transformTeaser($featuredContent)
        ];
    }

    private function transformImage(FeaturedContentContract $featuredContent)
    {
        if ($image = $featuredContent->getImage()) {
            return with(new ImageTransformer)->transform($image);
        }

        return null;
    }

    private function transformVideo(FeaturedContentContract $featuredContent)
    {
        if ($video = $featuredContent->getVideo()) {
            return with(new NativeVideoTransformer)->transform($video);
        }

        return null;
    }

    private function transformTeaser(FeaturedContentContract $featuredContent)
    {
        if ($composite = $featuredContent->getComposite()) {
            return with(new CompositeTeaserTransformer)->transform($composite);
        }

        return null;
    }
}
