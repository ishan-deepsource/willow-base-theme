<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\LeadParagraphContract;

/**
 * Class LeadParagraph
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 * @property LeadParagraphContract $model
 */
class LeadParagraph extends AbstractContent implements LeadParagraphContract
{
    public function __construct(LeadParagraphContract $leadParagraph)
    {
        parent::__construct($leadParagraph);
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

    public function getTextBlock(): ?string
    {
        return $this->model->getTextBlock();
    }

    public function getDisplayHint(): string
    {
        return $this->model->getDisplayHint();
    }
}
