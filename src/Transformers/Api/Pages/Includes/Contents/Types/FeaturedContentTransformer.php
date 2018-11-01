<?php

namespace Bonnier\Willow\Base\Transformers\Api\Pages\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\FeaturedContentContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\NativeVideoTransformer;
use League\Fractal\TransformerAbstract;

class FeaturedContentTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'teaser'
    ];

    protected $availableIncludes = [
        'teaser'
    ];

    public function transform(FeaturedContentContract $featuredContent)
    {
        return [
            'image' => $this->transformImage($featuredContent),
            'video' => $this->transformVideo($featuredContent),
            'display_hint' => $featuredContent->getDisplayHint()
        ];
    }

    public function includeTeaser(FeaturedContentContract $featuredContent)
    {
        if ($composite = $featuredContent->getComposite()) {
            return $this->item($composite, new CompositeTeaserTransformer());
        }

        return null;
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
}
