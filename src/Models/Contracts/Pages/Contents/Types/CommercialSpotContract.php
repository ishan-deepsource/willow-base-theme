<?php

namespace Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

interface CommercialSpotContract extends ContentContract
{
    public function getTitle(): ?string;
    public function getDescription(): ?string;
    public function getImage(): ?ImageContract;
    public function getLink(): ?HyperlinkContract;
}
