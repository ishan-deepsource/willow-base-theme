<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\InsertedCodeContract;

/**
 * Class Image
 *
 * @property InsertedCodeContract $model
 *
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 */
class InsertedCode extends AbstractContent implements InsertedCodeContract
{
    /**
     * InsertedCode constructor.
     *
     * @param \Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\InsertedCodeContract $insertedCode
     */
    public function __construct(InsertedCodeContract $insertedCode)
    {
        parent::__construct($insertedCode);
    }

    public function getCode(): string
    {
        return $this->model->getCode();
    }
    
    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }
}
