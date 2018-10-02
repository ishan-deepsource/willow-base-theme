<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\QuoteContract;

/**
 * Class Link
 *
 * @property QuoteContract $model
 *
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 */
class Quote extends AbstractContent implements QuoteContract
{
    /**
     * Link constructor.
     *
     * @param \Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\QuoteContract $quote
     */
    public function __construct(QuoteContract $quote)
    {
        parent::__construct($quote);
    }

    public function getQuote(): string
    {
        return $this->model->getQuote();
    }

    public function getAuthor(): ?string
    {
        return $this->model->getAuthor();
    }

    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }
}
