<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Illuminate\Support\Collection;

interface RecipeContract extends ContentContract
{
    public function getTitle(): ?string;

    public function getDescription(): ?string;

    public function getImage(): ?ImageContract;

    public function getUseAsArticleLeadImage(): ?bool;

    public function getShowMetaInfoInHeaderAndTeaser(): ?bool;

    public function getPreparationTime(): ?string;

    public function getPreparationTimeMin(): ?string;

    public function getPreparationTimeUnit(): string;

    public function getCookingTime(): ?string;

    public function getCookingTimeMin(): ?string;

    public function getCookingTimeUnit(): string;

    public function getTotalTime(): ?string;

    public function getTotalTimeMin(): ?string;

    public function getTotalTimeUnit(): string;

    public function getTotalTimeExtraInfo(): ?string;

    public function getQuantity(): ?string;

    public function getQuantityType(): ?string;

    public function getIngredientBlockItems(): Collection;

    public function getInstructionsHeadline(): ?string;

    public function getInstructions(): ?string;

    public function getInstructionsTip(): ?string;

    public function getNutrientsHeadline(): ?string;

    public function getNutrientItems(): Collection;

    public function getTags(): ?string;
}