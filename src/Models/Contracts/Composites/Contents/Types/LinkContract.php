<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;

interface LinkContract extends ContentContract
{
    public function getTitle(): ?string;

    public function getUrl(): ?string;

    public function getTarget(): ?string;
}
