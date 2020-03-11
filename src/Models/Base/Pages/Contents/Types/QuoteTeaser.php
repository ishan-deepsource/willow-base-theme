<?php

namespace Bonnier\Willow\Base\Models\Base\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Pages\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\QuoteTeaserContract;

/**
 * Class QuoteTeaser
 * @package Bonnier\Willow\Base\Models\Base\Pages\Contents\Types
 * @property QuoteTeaser $model
 */
class QuoteTeaser extends AbstractContent implements QuoteTeaserContract
{

    public function getQuote(): string
    {
        return $this->model->getQuote();
    }

    public function getAuthor(): ?string
    {
        return $this->model->getAuthor();
    }

    public function getLinkLabel(): ?string
    {
        return $this->model->getLinkLabel();
    }

    public function getLink(): ?string
    {
        return $this->model->getLink();
    }

    public function getComposite(): ?CompositeContract
    {
        return $this->model->getComposite();
    }
}