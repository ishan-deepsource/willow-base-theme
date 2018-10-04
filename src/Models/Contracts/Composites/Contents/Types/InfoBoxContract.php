<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;

interface InfoBoxContract extends ContentContract
{
    public function getTitle(): ?string;

    public function getBody(): ?string;
}
