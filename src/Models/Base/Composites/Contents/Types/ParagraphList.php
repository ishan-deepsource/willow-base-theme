<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ParagraphListContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Illuminate\Support\Collection;

/**
 * Class ParagraphList
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 * @property ParagraphListContract $model
 */
class ParagraphList extends AbstractContent implements ParagraphListContract
{
    public function __construct(ParagraphListContract $content)
    {
        parent::__construct($content);
    }

    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }

    public function getTitle(): ?string
    {
        return $this->model->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->model->getDescription();
    }

    public function getImage(): ?ImageContract
    {
        return $this->model->getImage();
    }

    public function getDisplayHint(): ?string
    {
        return $this->model->getDisplayHint();
    }

    public function getItems(): Collection
    {
        return $this->model->getItems();
    }
}
