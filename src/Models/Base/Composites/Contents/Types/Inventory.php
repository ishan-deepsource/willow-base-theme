<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\InventoryContract;
use Illuminate\Support\Collection;

/**
 * Class Inventory
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 * @property InventoryContract $model
 */
class Inventory extends AbstractContent implements InventoryContract
{
    public function __construct(InventoryContract $content)
    {
        parent::__construct($content);
    }

    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }

    public function getTitle(): ?string
    {
        return $this->model->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->model->getDescription();
    }

    public function getItems(): Collection
    {
        return $this->model->getItems();
    }
}
