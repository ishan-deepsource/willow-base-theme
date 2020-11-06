<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

interface InfoBoxContract extends ContentContract
{
    public function getTitle(): ?string;

    public function getBody(): ?string;

    public function getImage(): ?ImageContract;

    public function getDisplayHint(): string;
}
