<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\RecipeIngredientBlockItemContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\RecipeIngredientItemContract;
use League\Fractal\TransformerAbstract;

class RecipeIngredientBlockItemTransformer extends TransformerAbstract
{
    public function transform(RecipeIngredientBlockItemContract $recipeIngredientBlockItem)
    {
        return [
            'headline' => $recipeIngredientBlockItem->getHeadline(),
            'ingredient_items' => $this->transformItems($recipeIngredientBlockItem),
        ];
    }

    private function transformItems(RecipeIngredientBlockItemContract  $recipeIngredientBlockItem)
    {
        $ingredientItems = $recipeIngredientBlockItem->getIngredientItems();
        return $ingredientItems
            ->map(function (RecipeIngredientItemContract $recipeIngredientItem) {
                return with(new RecipeIngredientItemTransformer())
                    ->transform($recipeIngredientItem);
            });
    }
}
