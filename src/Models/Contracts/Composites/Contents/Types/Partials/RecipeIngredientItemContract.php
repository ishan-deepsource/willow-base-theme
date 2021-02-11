<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials;

interface RecipeIngredientItemContract
{
    public function getAmount(): ?string;

    public function getUnit(): string;

    public function getIngredient(): ?string;
}
