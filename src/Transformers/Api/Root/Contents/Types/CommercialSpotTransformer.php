<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\CommercialSpotContract;
use Bonnier\Willow\Base\Transformers\Api\Root\HyperlinkTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use League\Fractal\TransformerAbstract;

class CommercialSpotTransformer extends TransformerAbstract
{

    public function transform(CommercialSpotContract $commercialSpot)
    {
        return [
            'title' => $commercialSpot->getTitle(),
            'description' => $commercialSpot->getDescription(),
            'image' => $this->transformImage($commercialSpot),
            'link' => $this->transformLink($commercialSpot),
            'label' => $commercialSpot->getLabel(),
        ];
    }

    private function transformImage(CommercialSpotContract $teaserList)
    {
        if (optional($teaserList->getImage())->getUrl()) {
            return with(new ImageTransformer)->transform($teaserList->getImage());
        }

        return null;
    }

    private function transformLink(CommercialSpotContract $teaserList)
    {
        if (optional($teaserList->getLink())->getUrl()) {
            return with(new HyperlinkTransformer)->transform($teaserList->getLink());
        }

        return null;
    }

}
