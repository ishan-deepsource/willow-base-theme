<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types\Partials;

use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types\TeaserListAdapter;
use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;

class CommercialSpotHyperlink implements HyperlinkContract
{
    protected $label;
    protected $link;

    public function __construct(?string $link, ?string $label)
    {
        $this->link = $link;
        $this->label = $label;
    }

    public function getTitle(): ?string
    {
        return $this->label;
    }

    public function getUrl(): ?string
    {
        return $this->link;
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
