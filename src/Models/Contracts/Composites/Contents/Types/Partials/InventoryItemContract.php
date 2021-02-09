<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials;

interface InventoryItemContract
{
    public function getDisplayHint(): ?string;

    public function getKey(): ?string;

    public function getValue(): ?string;
}
