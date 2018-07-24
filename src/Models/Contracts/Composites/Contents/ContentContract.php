<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents;

interface ContentContract
{
    public function getType() : string;

    public function isLocked() : bool;

    public function getStickToNext(): bool;
}
