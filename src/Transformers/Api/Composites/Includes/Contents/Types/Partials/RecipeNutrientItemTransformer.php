<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\RecipeNutrientItemContract;
use League\Fractal\TransformerAbstract;

class RecipeNutrientItemTransformer extends TransformerAbstract
{
    public function transform(RecipeNutrientItemContract $recipeNutrientItem)
    {
        return [
            'nutrient' => $recipeNutrientItem->getNutrient(),
            'amount' => $recipeNutrientItem->getAmount(),
            'unit' => $recipeNutrientItem->getUnit(),
        ];
    }
}
