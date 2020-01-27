<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\QuoteContract;
use League\Fractal\TransformerAbstract;

/**
 * Class QuoteTransformer
 *
 * @package \Bonnier\Willow\Base\Transformers\Api\Composites\Partials
 */
class QuoteTransformer extends TransformerAbstract
{
    public function transform(QuoteContract $quote)
    {
        return [
            'quote' => $quote->isLocked() ? null : $quote->getQuote(),
            'author' => $quote->isLocked() ? null : $quote->getAuthor(),
        ];
    }
}
