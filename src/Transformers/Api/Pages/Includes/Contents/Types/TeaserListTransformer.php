<?php

namespace Bonnier\Willow\Base\Transformers\Api\Pages\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\TeaserListContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\HyperlinkTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use League\Fractal\TransformerAbstract;

class TeaserListTransformer extends TransformerAbstract
{
    public function transform(TeaserListContract $teaserList)
    {
        return [
            'title' => $teaserList->getTitle(),
            'description' => $teaserList->getDescription(),
            'background_image' => $this->transformImage($teaserList),
            'link' => $this->transformLink($teaserList),
            'display_hint' => $teaserList->getDisplayHint(),
            'teasers' => $this->transformTeasers($teaserList),
        ];
    }

    private function transformImage(TeaserListContract $teaserList)
    {
        if (optional($teaserList->getBackgroundImage())->getUrl()) {
            return with(new ImageTransformer)->transform($teaserList->getBackgroundImage());
        }

        return null;
    }

    private function transformLink(TeaserListContract $teaserList)
    {
        if (optional($teaserList->getLink())->getUrl()) {
            return with(new HyperlinkTransformer)->transform($teaserList->getLink());
        }

        return null;
    }

    private function transformTeasers(TeaserListContract $teaserList)
    {
        if (optional($teaserList->getTeasers())->isNotEmpty()) {
            return $teaserList->getTeasers()->map(function (CompositeContract $composite) {
                return with(new CompositeTeaserTransformer)->transform($composite);
            });
        }

        return null;
    }
}
