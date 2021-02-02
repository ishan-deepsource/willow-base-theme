<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\InventoryItemContract;

class InventoryItem implements InventoryItemContract
{
    private $item;

    public function __construct(InventoryItemContract $item)
    {
        $this->item = $item;
    }

    public function getDisplayHint(): ?string
    {
        return $this->item->getDisplayHint();
    }

    public function getKey(): ?string
    {
        return $this->item->getKey();
    }

    public function getValue(): ?string
    {
        return $this->item->getValue();
    }
}
