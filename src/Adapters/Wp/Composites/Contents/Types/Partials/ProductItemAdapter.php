<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\ProductItemContract;

class ProductItemAdapter implements ProductItemContract
{
    private $item;

    public function __construct($item)
    {
        $this->item = $item;
    }

    public function getParameter(): ?string
    {
        return array_get($this->item, 'parameter') ?: null;
    }

    public function getScore(): ?string
    {
        return array_get($this->item, 'score') ?: null;
    }
}
