<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use Illuminate\Support\Collection;

interface AssociatedCompositesContract extends ContentContract
{
    public function getTitle(): ?string;

    public function getComposites() : ?Collection;

    public function getDisplayHint(): ?string;
}
