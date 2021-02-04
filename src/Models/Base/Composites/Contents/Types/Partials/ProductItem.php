<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\ProductItemContract;

class ProductItem implements ProductItemContract
{
    private $item;

    public function __construct(ProductItemContract $item)
    {
        $this->item = $item;
    }

    public function getParameter(): ?string
    {
        return $this->item->getParameter();
    }

    public function getScore(): ?string
    {
        return $this->item->getScore();
    }
}
