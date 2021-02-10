<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\ProductDetailsItemContract;

class ProductDetailsItem implements ProductDetailsItemContract
{
    private $item;

    public function __construct(ProductDetailsItemContract $item)
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
