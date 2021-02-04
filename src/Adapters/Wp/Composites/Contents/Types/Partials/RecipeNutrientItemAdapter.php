<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\RecipeNutrientItemContract;

class RecipeNutrientItemAdapter implements RecipeNutrientItemContract
{
    private $item;

    public function __construct($item)
    {
        $this->item = $item;
    }

    public function getNutrient(): ?string
    {
        return array_get($this->item, 'nutrient') ?: null;
    }

    public function getAmount(): ?string
    {
        return array_get($this->item, 'amount') ?: null;
    }

    public function getUnit(): ?string
    {
        return array_get($this->item, 'unit') ?: null;
    }
}
