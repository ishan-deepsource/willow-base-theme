<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\CalculatorContract;

/**
 * Class Calculator
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 * @property CalculatorContract $model
 */
class Calculator extends AbstractContent implements CalculatorContract
{
    public function __construct(CalculatorContract $content)
    {
        parent::__construct($content);
    }

    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }

    public function getCalculator(): string
    {
        return $this->model->getCalculator();
    }
}
