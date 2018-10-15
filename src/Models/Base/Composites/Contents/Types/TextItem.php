<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\TextItemContract;

/**
 * Class Image
 *
 * @property TextItemContract $model
 *
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 */
class TextItem extends AbstractContent implements TextItemContract
{
    /**
     * TextItem constructor.
     *
     * @param \Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\TextItemContract $textItem
     */
    public function __construct(TextItemContract $textItem)
    {
        parent::__construct($textItem);
    }

    public function getBody(): ?string
    {
        return $this->model->getBody();
    }

    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }
}
