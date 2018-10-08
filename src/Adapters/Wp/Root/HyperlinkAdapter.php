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
        return optional($this->image)->getTitle() ?: null;
    }

    public function getUrl(): ?string
    {
        return optional($this->image)->getUrl() ?: null;
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
