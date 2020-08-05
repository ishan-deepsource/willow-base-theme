<?php

namespace Bonnier\Willow\Base\Models\Base\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;

class Teaser implements TeaserContract
{
    protected $teaser;

    public function __construct(TeaserContract $teaser)
    {
        $this->teaser = $teaser;
    }

    public function getTitle(): ?string
    {
        return $this->teaser->getTitle();
    }

    public function getImage(): ?ImageContract
    {
        return $this->teaser->getImage();
    }

    public function getVideoUrl() : ?string
    {
        return $this->teaser->getVideoUrl();
    }

    public function getDescription(): ?string
    {
        return $this->teaser->getDescription();
    }

    public function getType(): ?string
    {
        return $this->teaser->getType();
    }
}
