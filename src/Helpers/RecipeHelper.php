<?php

namespace Bonnier\Willow\Base\Helpers;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeRecipeMetaTransformer;

class RecipeHelper
{
    public function addRecipeMetaToOutput(CompositeContract $composite, array &$out)
    {
        if ($composite->getTemplate() == 'recipe') {
            $recipe = with(new CompositeRecipeMetaTransformer)->transform($composite);
            if (!empty($recipe))
                $out['recipe_meta'] = $recipe;
        }

    }
}