<?php

namespace Bonnier\Willow\Base\Models\Contracts\Root;

use Illuminate\Support\Collection;

interface SitemapCollectionContract
{
    public function getType(): string;

    public function getItems(): Collection;
}
