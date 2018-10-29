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
    protected $availableIncludes = [
        'teasers'
    ];

    protected $defaultIncludes = [
        'teasers'
    ];


    public function transform(TeaserListContract $teaserList)
    {
        return [
            'title' => $teaserList->getTitle(),
            'label' => $teaserList->getLabel(),
            'description' => $teaserList->getDescription(),
            'image' => $this->transformImage($teaserList),
            'link' => $this->transformLink($teaserList),
            'link_label' => $teaserList->getLinkLabel(),
            'display_hint' => $teaserList->getDisplayHint(),

        ];
    }

    private function transformImage(TeaserListContract $teaserList)
    {
        if (optional($teaserList->getImage())->getUrl()) {
            return with(new ImageTransformer)->transform($teaserList->getImage());
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

    public function includeTeasers(TeaserListContract $teaserList)
    {
        if (optional($teaserList->getTeasers())->isNotEmpty()) {
            return $this->collection($teaserList->getTeasers(), new CompositeTeaserTransformer);
        }

        return null;
    }

}
