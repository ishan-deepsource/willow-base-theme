<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\InventoryContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\InventoryItemContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\Partials\InventoryItemTransformer;
use League\Fractal\TransformerAbstract;

class InventoryTransformer extends TransformerAbstract
{
    public function transform(InventoryContract $inventory)
    {
        return [
            'title' => $inventory->getTitle(),
            'description' => $inventory->getDescription(),
            'items' => $this->transformItems($inventory)
        ];
    }

    private function transformItems(InventoryContract $inventory)
    {
        return $inventory->getItems()->map(function (InventoryItemContract $inventoryItem) {
            return with(new InventoryItemTransformer())->transform($inventoryItem);
        });
    }
}
