<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials\RecipeIngredientItem;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\RecipeIngredientBlockItemContract;
use Illuminate\Support\Collection;

class RecipeIngredientBlockItemAdapter implements RecipeIngredientBlockItemContract
{
    private $item;

    public function __construct($item)
    {
        $this->item = $item;
    }

    public function getHeadline(): ?string
    {
        return array_get($this->item, 'headline') ?: null;
    }

    public function getIngredientItems(): Collection
    {
        $arr = array_get($this->item, 'ingredient_items', []);
        return collect($arr)->map(function ($item) {
            return new RecipeIngredientItem(new RecipeIngredientItemAdapter($item));
        })->reject(function (RecipeIngredientItem $item) {
            return is_null($item->getAmount()) &&
                is_null($item->getUnit()) &&
                is_null($item->getIngredient());
        });
    }
}
