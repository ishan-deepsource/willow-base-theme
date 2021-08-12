<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\SeoTextContract;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use League\Fractal\TransformerAbstract;

class SeoTextTransformer extends TransformerAbstract
{
    public function transform(SeoTextContract $seoText)
    {
        return [
            'title' => $seoText->getTitle(),
            'description' => $seoText->getDescription(),
            'image' => $this->transformImage($seoText),
            'image_position' => $seoText->getImagePosition(),
            'link' => $seoText->getLink(),
            'link_target' => $seoText->getLinkTarget(),
            'link_rel' => $seoText->getLinkRel(),
        ];
    }

    private function transformImage(SeoTextContract $seoText)
    {
        if ($image = $seoText->getImage()) {
            return with(new ImageTransformer)->transform($image);
        }

        return null;
    }
}
