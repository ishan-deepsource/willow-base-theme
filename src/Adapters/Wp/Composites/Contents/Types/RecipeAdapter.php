<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials\RecipeIngredientBlockItemAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials\RecipeNutrientItemAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials\RecipeIngredientBlockItem;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials\RecipeNutrientItem;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\RecipeContract;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Illuminate\Support\Collection;

class RecipeAdapter extends AbstractContentAdapter implements RecipeContract
{
    public function getTitle(): ?string
    {
        return array_get($this->acfArray, 'title') ?: null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->acfArray, 'description') ?: null;
    }

    public function getImage(): ?ImageContract
    {
        if ($imageArray = array_get($this->acfArray, 'image')) {
            $image = WpModelRepository::instance()->getPost($imageArray);
            return new Image(new ImageAdapter($image));
        }

        return null;
    }

    public function getTags(): ?string
    {
        return array_get($this->acfArray, 'recipe_tags') ?: null;
    }

    public function getUseAsArticleLeadImage(): ?bool
    {
        return boolval(array_get($this->acfArray, 'use_as_article_lead_image', false));
    }

    public function getShowMetaInfoInHeaderAndTeaser(): ?bool
    {
        return boolval(array_get($this->acfArray, 'show_meta_info_in_header_and_teaser', false));
    }

    public function getPreparationTime(): ?string
    {
        return array_get($this->acfArray, 'preparation_time') ?: null;
    }

    public function getPreparationTimeMin(): ?string
    {
        return array_get($this->acfArray, 'preparation_time_min') ?: null;
    }

    public function getPreparationTimeUnit(): string
    {
        return array_get($this->acfArray, 'preparation_time_unit') ?: 'm';
    }

    public function getCookingTime(): ?string
    {
        return array_get($this->acfArray, 'cooking_time') ?: null;
    }

    public function getCookingTimeMin(): ?string
    {
        return array_get($this->acfArray, 'cooking_time_min') ?: null;
    }

    public function getCookingTimeUnit(): string
    {
        return array_get($this->acfArray, 'cooking_time_unit') ?: 'm';
    }

    public function getTotalTime(): ?string
    {
        return array_get($this->acfArray, 'total_time') ?: null;
    }

    public function getTotalTimeMin(): ?string
    {
        return array_get($this->acfArray, 'total_time_min') ?: null;
    }

    public function getTotalTimeUnit(): string
    {
        return array_get($this->acfArray, 'total_time_unit') ?: 'm';
    }

    public function getTotalTimeExtraInfo(): ?string
    {
        return array_get($this->acfArray, 'total_time_extra_info') ?: null;
    }

    public function getQuantity(): ?string
    {
        return array_get($this->acfArray, 'quantity') ?: null;
    }

    public function getQuantityType(): ?string
    {
        return array_get($this->acfArray, 'quantity_type') ?: null;
    }

    public function getIngredientBlockItems(): Collection
    {
        $arr = array_get($this->acfArray, 'ingredient_block_items', []);
        return collect($arr)->map(function ($item) {
            return new RecipeIngredientBlockItem(new RecipeIngredientBlockItemAdapter($item));
        })->reject(function (RecipeIngredientBlockItem $item) {
            return is_null($item->getHeadline()) &&
                count($item->getIngredientItems()) == 0;
        });
    }

    public function getInstructionsHeadline(): ?string
    {
        return array_get($this->acfArray, 'instructions_headline') ?: null;
    }

    public function getInstructions(): ?string
    {
        return array_get($this->acfArray, 'instructions') ?: null;
    }

    public function getInstructionsTip(): ?string
    {
        return array_get($this->acfArray, 'instructions_tip') ?: null;
    }

    public function getNutrientsHeadline(): ?string
    {
        return array_get($this->acfArray, 'nutrients_headline') ?: null;
    }

    public function getNutrientItems(): Collection
    {
        $arr = array_get($this->acfArray, 'nutrient_items', []);
        return collect($arr)->map(function ($item) {
            return new RecipeNutrientItem(new RecipeNutrientItemAdapter($item));
        })->reject(function (RecipeNutrientItem $item) {
            return is_null($item->getNutrient()) &&
                $item->getUnit() == '-' &&
                is_null($item->getAmount());
        });
    }
}