<?php

namespace Bonnier\Willow\Base\Models\Contracts\Search;

interface FacetContract
{
    public function getLabel(): string;
    public function getCount(): int;
    public function isActive(): bool;
}
