<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\SitemapCollectionContract;
use Bonnier\Willow\Base\Models\Contracts\Root\SitemapItemContract;
use League\Fractal\TransformerAbstract;

class SitemapTransformer extends TransformerAbstract
{
    public function transform(SitemapCollectionContract $sitemapCollection)
    {
        return [
            'type' => $sitemapCollection->getType(),
            'items' => $sitemapCollection->getItems()->map(function (SitemapItemContract $sitemapItem) {
                return with(new SitemapItemTransformer)->transform($sitemapItem);
            }),
        ];
    }
}
