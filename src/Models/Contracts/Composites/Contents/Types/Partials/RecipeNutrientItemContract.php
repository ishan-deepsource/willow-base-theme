<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials;

interface RecipeNutrientItemContract
{
    public function getNutrient(): string;

    public function getAmount(): ?string;

    public function getUnit(): string;
}
