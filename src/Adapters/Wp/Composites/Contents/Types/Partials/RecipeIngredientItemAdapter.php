<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\RecipeIngredientItemContract;

class RecipeIngredientItemAdapter implements RecipeIngredientItemContract
{
    private $item;

    public function __construct($item)
    {
        $this->item = $item;
    }

    public function getAmount(): ?string
    {
        return array_get($this->item, 'amount') ?: null;
    }

    public function getUnit(): string
    {
        return array_get($this->item, 'unit') ?: '-';
    }

    public function getIngredient(): ?string
    {
        return array_get($this->item, 'ingredient') ?: null;
    }
}
