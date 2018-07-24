<?php

namespace Bonnier\Willow\Base\Models\Base\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\SitemapCollectionContract;
use Illuminate\Support\Collection;

class SitemapCollection implements SitemapCollectionContract
{
    protected $sitemapCollection;

    /**
     * SitemapCollection constructor.
     * @param $sitemapCollection
     */
    public function __construct(SitemapCollectionContract $sitemapCollection)
    {
        $this->sitemapCollection = $sitemapCollection;
    }

    public function getType(): string
    {
        return $this->sitemapCollection->getType();
    }

    public function getItems(): Collection
    {
        return $this->sitemapCollection->getItems();
    }
}
