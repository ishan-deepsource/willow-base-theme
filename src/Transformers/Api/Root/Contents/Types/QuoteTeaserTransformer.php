<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\QuoteTeaserContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTransformer;
use League\Fractal\TransformerAbstract;

class QuoteTeaserTransformer extends TransformerAbstract
{

    public function transform(QuoteTeaserContract $quoteTeaser)
    {
        return [
            'quote' => $quoteTeaser->getQuote(),
            'author' => $quoteTeaser->getAuthor(),
            'link_label' => $quoteTeaser->getLinkLabel(),
            'link' => $quoteTeaser->getLink(),
            'composite' => $this->getComposite($quoteTeaser)
        ];
    }

    private function getComposite(QuoteTeaserContract $quoteTeaser)
    {
        if ($composite = $quoteTeaser->getComposite()) {
            return with(new CompositeTeaserTransformer)->transform($composite);
        }
        return null;
    }
}