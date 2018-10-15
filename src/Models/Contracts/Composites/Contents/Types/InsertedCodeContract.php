<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;

interface InsertedCodeContract extends ContentContract
{
    public function getCode(): ?string;
}
