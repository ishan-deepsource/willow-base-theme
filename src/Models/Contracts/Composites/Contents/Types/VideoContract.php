<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Illuminate\Support\Collection;

interface VideoContract extends ContentContract
{
    public function getEmbedUrl(): ?string;

    public function getCaption(): ?string;

    public function getChapterItems(): Collection;

    // todo: maybe implement getThumbnail?
}
