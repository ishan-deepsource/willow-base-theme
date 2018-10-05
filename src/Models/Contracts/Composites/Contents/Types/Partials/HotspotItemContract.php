<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials;

interface HotspotItemContract
{
    public function getTitle(): ?string;
    public function getDescription(): ?string;
    public function getCoordinates(): array;
}
