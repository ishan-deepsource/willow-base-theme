<?php

namespace Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Illuminate\Support\Collection;

interface TeaserListContract extends ContentContract
{
    public function getTitle(): ?string;
    public function getDescription(): ?string;
    public function getBackgroundImage(): ?ImageContract;
    public function getLink(): ?HyperlinkContract;
    public function getDisplayHint(): ?string;
    public function getTeasers(): ?Collection;
}
