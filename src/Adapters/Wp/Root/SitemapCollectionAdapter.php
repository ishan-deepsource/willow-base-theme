<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\SitemapCollectionContract;
use Illuminate\Support\Collection;

class SitemapCollectionAdapter implements SitemapCollectionContract
{
    protected $type;
    protected $items;

    /**
     * SitemapCollectionAdapter constructor.
     * @param $type
     * @param $items
     */
    public function __construct(string $type, Collection $items)
    {
        $this->type = $type;
        $this->items = $items;
    }


    public function getType(): string
    {
        return $this->type;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }
}
