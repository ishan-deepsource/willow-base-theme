<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials\InventoryItemAdapter;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials\InventoryItem;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\InventoryContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\InventoryItemContract;
use Illuminate\Support\Collection;

class InventoryAdapter extends AbstractContentAdapter implements InventoryContract
{
    public function getTitle(): ?string
    {
        return array_get($this->acfArray, 'title') ?: null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->acfArray, 'description') ?: null;
    }

    public function getItems(): Collection
    {
        return collect(array_get($this->acfArray, 'items', []))->map(function ($item) {
            return new InventoryItem(new InventoryItemAdapter($item));
        })->reject(function (InventoryItemContract $item) {
            return is_null($item->getDisplayHint()) &&
                is_null($item->getKey()) &&
                is_null($item->getValue());
        });
    }
}
