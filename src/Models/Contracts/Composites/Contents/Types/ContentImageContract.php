<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

interface ContentImageContract extends ContentContract, ImageContract
{
    public function isLead() : bool;

    public function getVideoUrl(): ?string;
}
