<?php

namespace Bonnier\Willow\Base\Helpers;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeRecipeTransformer;

class RecipeHelper
{
    public function addRecipeMetaToOutput(CompositeContract $composite, array &$out)
    {
        $recipe =  with(new CompositeRecipeTransformer)->transform($composite);
        if (!empty($recipe))
            $out['recipe_meta'] = $recipe;
    }
}