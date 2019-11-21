<?php

namespace Bonnier\Willow\Base\Models\Base\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\SitemapItemContract;
use DateTime;

class SitemapItem implements SitemapItemContract
{
    protected $sitemapItem;

    /**
     * Sitemap constructor.
     * @param $sitemapItem
     */
    public function __construct(SitemapItemContract $sitemapItem)
    {
        $this->sitemapItem = $sitemapItem;
    }


    public function getUrl(): ?string
    {
        return $this->sitemapItem->getUrl();
    }

    public function getLastModified(): DateTime
    {
        return $this->sitemapItem->getLastModified();
    }
}
