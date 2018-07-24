<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\SitemapItemContract;
use League\Fractal\TransformerAbstract;

class SitemapItemTransformer extends TransformerAbstract
{
    public function transform(SitemapItemContract $sitemapItem)
    {
        return [
            'loc' => $sitemapItem->getUrl(),
            'lastmod' => $sitemapItem->getLastModified()->format('c')
        ];
    }
}
