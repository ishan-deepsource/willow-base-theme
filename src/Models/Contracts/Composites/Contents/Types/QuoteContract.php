<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;

interface QuoteContract extends ContentContract
{
    public function getQuote(): ?string;

    public function getAuthor(): ?string;
}
