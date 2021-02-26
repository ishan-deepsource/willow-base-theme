<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Root\FileContract;
use Illuminate\Support\Collection;

interface ContentFileContract extends ContentContract
{
    public function getTitle(): ?string;

    public function getDescription(): ?string;

    public function getFile(): ?FileContract;

    public function getImages() : ?Collection;

    public function getDownloadButtonText() : ?string;
}
