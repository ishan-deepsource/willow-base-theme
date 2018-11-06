<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\StoryContract;
use Illuminate\Support\Collection;

class Story implements StoryContract
{
    protected $story;

    public function __construct(StoryContract $story)
    {
        $this->story = $story;
    }

    public function getTeaser(): ?CompositeContract
    {
        return $this->story->getTeaser();
    }

    public function getArticles(): ?Collection
    {
        return $this->story->getArticles();
    }
}
