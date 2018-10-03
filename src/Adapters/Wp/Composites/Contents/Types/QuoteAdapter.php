<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\QuoteContract;

/**
 * Class VideoAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class QuoteAdapter extends AbstractContentAdapter implements QuoteContract
{
    public function getQuote(): string
    {
        return $this->acfArray['quote'] ?? '';
    }

    public function getAuthor(): string
    {
        return $this->acfArray['author'] ?? '';
    }
}
