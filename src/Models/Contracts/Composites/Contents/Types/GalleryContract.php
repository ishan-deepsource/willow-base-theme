<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use Illuminate\Support\Collection;

interface GalleryContract extends ContentContract
{
    public function getTitle() : ?string;
    public function getDisplayHint(): ?string;
    public function getImages(): Collection;
}
