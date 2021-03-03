<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\CalculatorContract;

class CalculatorAdapter extends AbstractContentAdapter implements CalculatorContract
{
    public function getCalculator(): string
    {
        return array_get($this->acfArray, 'calculator', '');
    }
}
