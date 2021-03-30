<?php

namespace Bonnier\Willow\Base\Helpers;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;

/**
 * Class FoodPlanHelper
 * @package Bonnier\Willow\Base\Helpers
 */
class FoodPlanHelper
{
    /**
     * @param CompositeContract $composite
     * @param array $out
     * @desc adds week-number to output from permalink
     *  if page_template is 'food-plan'
     *  extract week-number-year from permalink
     * @example http://.../sunde-opskrifter/madplaner/madplan-uge-16-2021
     *  appends to output
     *  'week_number' => '16-2021'
     */
    public function addToOutput(CompositeContract $composite, array &$out) : void
    {
        if ($composite->getTemplate() == 'food-plan') {
            $pattern = '/.*[^0-9]([0-9]+-[0-9]+)$/';
            $canonicalUrl = $composite->getCanonicalUrl();
            $matches = [];
            if (preg_match($pattern, $canonicalUrl, $matches)) {
                if (!empty($matches[1])) {
                    $out['week_number'] = $matches[1];
                }
            }
        }
    }
}
