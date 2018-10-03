<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types\Partials;

use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types\TeaserListAdapter;
use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;

class TeaserListHyperlink implements HyperlinkContract
{
    protected $teaserList;
    protected $link;

    public function __construct(TeaserListAdapter $teaserList, ?string $link)
    {
        $this->teaserList = $teaserList;
        $this->link = $link;
    }

    public function getTitle(): ?string
    {
        return $this->teaserList->getTitle();
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
