<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\RecipeContract;
use Bonnier\WP\Cxense\WpCxense;
use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;

class CompositeRecipeMetaTransformer extends TransformerAbstract
{
    /**
     * @param CompositeContract $composite
     * @return array
     * @desc If $recipeTags is null or if checkbox is set - collect data
     * if checkbox is set return false to break out of loop and keep this data.
     * else keep first elements data.
     */
    public function transform(CompositeContract $composite) : array
    {
        $recipeMeta = [];

        $contents = collect($composite->getContents());
        $firstContent = $contents->first();

        if (is_array($firstContent) && isset($firstContent['type']) && $firstContent['type'] === 'cxense') {
            $this->transformCxenseRecipeMeta($firstContent, $recipeMeta);
        }
        else {
            $contents->each(function($content) use(&$recipeMeta) {
                if ($content->getType() == 'recipe')
                    return $this->transformRecipeMeta($content, $recipeMeta);
            });
        }

        return $recipeMeta;
    }

    private function transformCxenseRecipeMeta(array $content, array &$recipeMeta) : void
    {
        $orgPreFix = WpCxense::instance()->settings->getOrganisationPrefix();
        if (isset($content[$orgPreFix . '-recipe-meta-time']))
            $recipeMeta['time'] = $content[$orgPreFix . '-recipe-meta-time'];
        if (isset($content[$orgPreFix . '-recipe-meta-time-unit']))
            $recipeMeta['time_unit'] = $content[$orgPreFix . '-recipe-meta-time-unit'];
        if (isset($content[$orgPreFix . '-recipe-meta-energy']))
            $recipeMeta['energy'] = $content[$orgPreFix . '-recipe-meta-energy'];
        if (isset($content[$orgPreFix . '-recipe-meta-energy-unit']))
            $recipeMeta['energy_unit'] = $content[$orgPreFix . '-recipe-meta-energy-unit'];
    }

    private function transformRecipeMeta(RecipeContract $content, array &$recipeMeta) : bool
    {
        if (is_null($recipeMeta) || $content->getShowMetaInfoInHeaderAndTeaser()) {
            $recipeMeta = [
                'time' => $content->getTotalTimeMin(),
                'time_unit' => $content->getTotalTimeUnit(),
            ];

            $this->transformEnergyKcal($content, $recipeMeta);

            if ($content->getShowMetaInfoInHeaderAndTeaser()) {
                return false;
            }
        }

        return true;
    }

    private function transformEnergyKcal(RecipeContract $content, array &$recipeMeta) : void
    {
        $content->getNutrientItems()->each(function($nutrient) use(&$recipeMeta) {
            if ($nutrient->getNutrient() == 'Energy') {
                $recipeMeta['energy'] = $nutrient->getAmount();
                $recipeMeta['energy_unit'] = $nutrient->getUnit();
                return false;
            }
        });
    }
}
