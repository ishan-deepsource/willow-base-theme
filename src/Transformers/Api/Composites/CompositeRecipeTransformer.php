<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use League\Fractal\TransformerAbstract;

class CompositeRecipeTransformer extends TransformerAbstract
{
    public function transform(CompositeContract $composite)
    {
        $recipeTags = [];

        if ($composite->getTemplate() == 'recipe') {
            collect($composite->getContents())->each(function($content) use(&$recipeTags) {
                if ($content->getType() == 'recipe')
                    return $this->transformRecipeWidget($content, $recipeTags);
                return true;
            });
        }

        return $recipeTags;
    }

    private function transformRecipeWidget(ContentContract $content, array &$recipeTags)
    {
        /**
         * If $recipeTags is null or if checkbox is set - collect data
         * if checkbox is set return false to break out of loop and keep this data.
         * else keep first elements data.
         */
        if (is_null($recipeTags) || $content->getShowMetaInfoInHeaderAndTeaser()) {
            $recipeTags = [
                'time' => $content->getTotalTimeMin(),
                'time_unit' => $content->getTotalTimeUnit(),
            ];

            $content->getNutrientItems()->each(function($nutrient) use(&$recipeTags) {
                if ($nutrient->getNutrient() == 'Energy') {
                    $recipeTags['energy'] = $nutrient->getAmount();
                    $recipeTags['energy_unit'] = $nutrient->getUnit();
                }
            });

            if ($content->getShowMetaInfoInHeaderAndTeaser()) {
                return false;
            }
        }

        return true;
    }
}