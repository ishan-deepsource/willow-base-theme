<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Helpers\AcfOutput;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\RecipeIngredientItemContract;

class RecipeIngredientItemAdapter implements RecipeIngredientItemContract
{
    private $item;
    private $acfOutput;

    public function __construct($item)
    {
        $this->item = $item;
        $this->acfOutput = new AcfOutput($item);
    }

    public function getAmount(): ?string
    {
        return array_get($this->item, 'amount') ?: null;
    }

    public function getUnit(): string
    {
        return $this->acfOutput->getString('unit', '-');
    }

    public function getIngredient(): ?string
    {
        return array_get($this->item, 'ingredient') ?: null;
    }
}
