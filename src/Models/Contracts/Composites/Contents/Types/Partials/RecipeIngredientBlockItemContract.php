<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials;

use Illuminate\Support\Collection;

interface RecipeIngredientBlockItemContract
{
    public function getHeadline(): ?string;

    public function getIngredientItems(): Collection;
}
