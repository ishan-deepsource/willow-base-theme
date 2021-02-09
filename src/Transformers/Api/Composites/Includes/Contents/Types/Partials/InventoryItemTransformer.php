<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\InventoryItemContract;
use League\Fractal\TransformerAbstract;

class InventoryItemTransformer extends TransformerAbstract
{
    public function transform(InventoryItemContract $inventoryItem)
    {
        return [
            'display_hint' => $inventoryItem->getDisplayHint(),
            'key' => $inventoryItem->getKey(),
            'value' => $inventoryItem->getValue(),
        ];
    }
}
