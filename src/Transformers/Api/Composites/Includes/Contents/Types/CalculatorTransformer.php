<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\CalculatorContract;
use League\Fractal\TransformerAbstract;

class CalculatorTransformer extends TransformerAbstract
{
    public function transform(CalculatorContract $calculator)
    {
        return [
            'calculator' => $calculator->getCalculator(),
        ];
    }
}
