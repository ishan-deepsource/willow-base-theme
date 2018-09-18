<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;

class HyperlinkAdapter implements HyperlinkContract
{
    protected $image;

    public function __construct(ImageAdapter $image)
    {
        $this->image = $image;
    }

    public function getTitle(): ?string
    {
        return $this->image->getTitle();
    }

    public function getUrl(): ?string
    {
        return $this->image->getUrl();
    }

    public function getRelationship(): ?string
    {
        return null;
    }

    public function getTarget(): ?string
    {
        return null;
    }
}
