<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\RecipeNutrientItemContract;

class RecipeNutrientItem implements RecipeNutrientItemContract
{
    private $item;

    public function __construct(RecipeNutrientItemContract $item)
    {
        $this->item = $item;
    }

    public function getNutrient(): ?string
    {
        return $this->item->getNutrient();
    }

    public function getAmount(): ?string
    {
        return $this->item->getAmount();
    }

    public function getUnit(): ?string
    {
        return $this->item->getUnit();
    }
}
