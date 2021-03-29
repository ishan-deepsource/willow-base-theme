<?php

namespace Bonnier\Willow\Base\Helpers;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeRecipeMetaTransformer;

class RecipeMetaHelper
{
    public function addToOutput(CompositeContract $composite, array &$out) : void
    {
        if ($composite->getTemplate() == 'recipe') {
            $recipe = with(new CompositeRecipeMetaTransformer)->transform($composite);
            if (!empty($recipe))
                $out['recipe_meta'] = $recipe;
        }
    }
}
