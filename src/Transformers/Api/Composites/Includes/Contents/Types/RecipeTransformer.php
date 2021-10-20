<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\RecipeIngredientBlockItemContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\RecipeNutrientItemContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\RecipeContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\Partials\RecipeIngredientBlockItemTransformer;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\Partials\RecipeNutrientItemTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use League\Fractal\TransformerAbstract;

class RecipeTransformer extends TransformerAbstract
{
    public function transform(RecipeContract $recipe)
    {
        return [
            'title' => $recipe->getTitle(),
            'description' => $recipe->getDescription(),
            'image' => $this->transformImage($recipe),
            'category' => $recipe->getCategory(),
            'recipe_tags' => $recipe->getTags(),
            'use_as_article_lead_image' => $recipe->getUseAsArticleLeadImage(),
            'show_meta_info_in_header_and_teaser' => $recipe->getShowMetaInfoInHeaderAndTeaser(),
            'preparation_time' => $recipe->getPreparationTime(),
            'preparation_time_min' => $recipe->getPreparationTimeMin(),
            'preparation_time_unit' => $recipe->getPreparationTimeUnit(),
            'cooking_time' => $recipe->getCookingTime(),
            'cooking_time_min' => $recipe->getCookingTimeMin(),
            'cooking_time_unit' => $recipe->getCookingTimeUnit(),
            'total_time' => $recipe->getTotalTime(),
            'total_time_min' => $recipe->getTotalTimeMin(),
            'total_time_unit' => $recipe->getTotalTimeUnit(),
            'total_time_extra_info' => $recipe->getTotalTimeExtraInfo(),
            'quantity' => $recipe->getQuantity(),
            'quantity_type' => $recipe->getQuantityType(),
            'ingredient_block_items' => $this->transformIngredientBlockItems($recipe),
            'instructions_headline' => $recipe->getInstructionsHeadline(),
            'instructions' => $recipe->getInstructions(),
            'instructions_tip' => $recipe->getInstructionsTip(),
            'nutrients_headline' => $recipe->getNutrientsHeadline(),
            'nutrient_items' => $this->transformNutrientBlockItems($recipe),
        ];
    }

    private function transformImage(RecipeContract $recipe)
    {
        if ($image = $recipe->getImage()) {
            return with(new ImageTransformer)->transform($image);
        }

        return null;
    }

    private function transformIngredientBlockItems(RecipeContract  $recipe)
    {
        $ingredientBlockItems = $recipe->getIngredientBlockItems();
        return $ingredientBlockItems
            ->map(function (RecipeIngredientBlockItemContract $recipeIngredientBlockItem) {
                return with(new RecipeIngredientBlockItemTransformer())
                    ->transform($recipeIngredientBlockItem);
            });
    }

    private function transformNutrientBlockItems(RecipeContract $recipe)
    {
        $nutrientItems = $recipe->getNutrientItems();
        return $nutrientItems
            ->map(function (RecipeNutrientItemContract $recipeNutrientItem) {
                return with(new RecipeNutrientItemTransformer())
                    ->transform($recipeNutrientItem);
            });
    }
}
