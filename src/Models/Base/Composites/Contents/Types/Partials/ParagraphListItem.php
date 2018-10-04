<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\ParagraphListItemContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

class ParagraphListItem implements ParagraphListItemContract
{
    private $item;

    public function __construct(ParagraphListItemContract $item)
    {
        $this->item = $item;
    }

    public function getCustomBullet(): ?string
    {
        return $this->item->getCustomBullet();
    }

    public function getTitle(): ?string
    {
        return $this->item->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->item->getDescription();
    }

    public function getImage(): ?ImageContract
    {
        return $this->item->getImage();
    }
}
