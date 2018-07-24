<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

interface VideoContract extends ContentContract
{
    public function getEmbedUrl() : string;

    public function getCaption() : string;

    // todo: maybe implement getThumbnail?
}
