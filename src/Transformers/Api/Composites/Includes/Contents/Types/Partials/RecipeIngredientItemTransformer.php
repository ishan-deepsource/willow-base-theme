<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\RecipeIngredientItemContract;
use League\Fractal\TransformerAbstract;

class RecipeIngredientItemTransformer extends TransformerAbstract
{
    public function transform(RecipeIngredientItemContract $recipeIngredientItem)
    {
        return [
            'amount' => $recipeIngredientItem->getAmount(),
            'unit' => $recipeIngredientItem->getUnit(),
            'ingredient' => $recipeIngredientItem->getIngredient(),
        ];
    }
}
