<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Illuminate\Support\Collection;

interface StoryContract
{
    public function getTeaser(): ?CompositeContract;

    public function getArticles(): ?Collection;
}
