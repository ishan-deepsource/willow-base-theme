<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;

interface CalculatorContract extends ContentContract
{
    public function getCalculator(): ?string;
}
