<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use Illuminate\Support\Collection;

interface InventoryContract extends ContentContract
{
    public function getTitle(): ?string;

    public function getDescription(): ?string;

    public function getItems(): Collection;
}
