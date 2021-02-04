<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\RecipeIngredientBlockItemContract;
use Illuminate\Support\Collection;

class RecipeIngredientBlockItem implements RecipeIngredientBlockItemContract
{
    private $item;

    public function __construct(RecipeIngredientBlockItemContract $item)
    {
        $this->item = $item;
    }

    public function getHeadline(): ?string
    {
        return $this->item->getHeadline();
    }

    public function getIngredientItems(): Collection
    {
        return $this->item->getIngredientItems();
    }
}
