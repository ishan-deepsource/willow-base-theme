<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\TeaserListContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\HyperlinkTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use Bonnier\Willow\Base\Transformers\Pagination\NumberedPagination;
use Bonnier\Willow\Base\Transformers\Pagination\StringCursor;
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
            'can_paginate' => $teaserList->canPaginate(),
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
            $resource = $this->collection($teaserList->getTeasers(), new CompositeTeaserTransformer);
            if ($teaserList->canPaginate()) {
                $paginator = new NumberedPagination(
                    $teaserList->getPage(),
                    $teaserList->getTeasersPerPage(),
                    $teaserList->getTotalTeasers(),
                    $teaserList->getTeasers()->count(),
                    $teaserList->getTotalPages()
                );
                $cursor = new StringCursor();
                $cursor->setCount($teaserList->getTeasers()->count());
                $cursor->setNext($teaserList->getNextCursor());
                $cursor->setPrev($teaserList->getPreviousCursor());
                $cursor->setCurrent($teaserList->getCurrentCursor());
                $resource->setMeta([
                    'pagination' => $paginator->toArray(),
                    'cursor' => $cursor->toArray(),
                ]);
            }
            return $resource;
        }

        return null;
    }

}
