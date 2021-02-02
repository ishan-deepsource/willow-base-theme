<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\InventoryItemContract;

class InventoryItemAdapter implements InventoryItemContract
{
    private $item;

    public function __construct($item)
    {
        $this->item = $item;
    }

    public function getDisplayHint(): ?string
    {
        return array_get($this->item, 'display_hint') ?: null;
    }

    public function getKey(): ?string
    {
        return array_get($this->item, 'key') ?: null;
    }

    public function getValue(): ?string
    {
        return array_get($this->item, 'value') ?: null;
    }
}
