<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\RecipeIngredientItemContract;

class RecipeIngredientItem implements RecipeIngredientItemContract
{
    private $item;

    public function __construct(RecipeIngredientItemContract $item)
    {
        $this->item = $item;
    }

    public function getAmount(): ?string
    {
        return $this->item->getAmount();
    }

    public function getUnit(): ?string
    {
        return $this->item->getUnit();
    }

    public function getIngredient(): ?string
    {
        return $this->item->getIngredient();
    }
}
