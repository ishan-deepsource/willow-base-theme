<?php

namespace Bonnier\Willow\Base\Models\Base\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;

class Hyperlink implements HyperlinkContract
{
    protected $hyperlink;

    public function __construct(HyperlinkContract $hyperlink)
    {
        $this->hyperlink = $hyperlink;
    }

    public function getTitle(): ?string
    {
        return $this->hyperlink->getTitle();
    }

    public function getUrl(): ?string
    {
        return $this->hyperlink->getUrl();
    }

    public function getRelationship(): ?string
    {
        return $this->hyperlink->getRelationship();
    }

    public function getTarget(): ?string
    {
        return $this->hyperlink->getTarget();
    }
}
