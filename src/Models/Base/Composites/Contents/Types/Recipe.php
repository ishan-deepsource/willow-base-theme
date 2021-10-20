<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\RecipeContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Illuminate\Support\Collection;

/**
 * Class Recipe
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 * @property RecipeContract $model
 */
class Recipe extends AbstractContent implements RecipeContract
{
    public function __construct(RecipeContract $content)
    {
        parent::__construct($content);
    }

    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }

    public function getTitle(): ?string
    {
        return $this->model->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->model->getDescription();
    }

    public function getImage(): ?ImageContract
    {
        return $this->model->getImage();
    }

    public function getCategory(): ?string
    {
        return $this->model->getCategory();
    }

    public function getTags(): ?string
    {
        return $this->model->getTags();
    }

    public function getUseAsArticleLeadImage(): bool
    {
        return $this->model->getUseAsArticleLeadImage();
    }

    public function getShowMetaInfoInHeaderAndTeaser(): bool
    {
        return $this->model->getShowMetaInfoInHeaderAndTeaser();
    }

    public function getPreparationTime(): ?string
    {
        return $this->model->getPreparationTime();
    }

    public function getPreparationTimeMin(): ?string
    {
        return $this->model->getPreparationTimeMin();
    }

    public function getPreparationTimeUnit(): ?string
    {
        return $this->model->getPreparationTimeUnit();
    }

    public function getCookingTime(): ?string
    {
        return $this->model->getCookingTime();
    }

    public function getCookingTimeMin(): ?string
    {
        return $this->model->getCookingTimeMin();
    }

    public function getCookingTimeUnit(): ?string
    {
        return $this->model->getCookingTimeUnit();
    }

    public function getTotalTime(): ?string
    {
        return $this->model->getTotalTime();
    }

    public function getTotalTimeMin(): ?string
    {
        return $this->model->getTotalTimeMin();
    }

    public function getTotalTimeUnit(): ?string
    {
        return $this->model->getTotalTimeUnit();
    }

    public function getTotalTimeExtraInfo(): ?string
    {
        return $this->model->getTotalTimeExtraInfo();
    }

    public function getQuantity(): ?string
    {
        return $this->model->getQuantity();
    }

    public function getQuantityType(): ?string
    {
        return $this->model->getQuantityType();
    }

    public function getIngredientBlockItems(): Collection
    {
        return $this->model->getIngredientBlockItems();
    }

    public function getInstructionsHeadline(): ?string
    {
        return $this->model->getInstructionsHeadline();
    }

    public function getInstructions(): ?string
    {
        return $this->model->getInstructions();
    }

    public function getInstructionsTip(): ?string
    {
        return $this->model->getInstructionsTip();
    }

    public function getNutrientsHeadline(): ?string
    {
        return $this->model->getNutrientsHeadline();
    }

    public function getNutrientItems(): Collection
    {
        return $this->model->getNutrientItems();
    }
}