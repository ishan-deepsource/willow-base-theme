<?php

namespace Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Models\Contracts\Utilities\WidgetPaginationContract;
use Illuminate\Support\Collection;

interface TeaserListContract extends ContentContract, WidgetPaginationContract
{
    public function getTitle(): ?string;
    public function getLabel(): ?string;
    public function getDescription(): ?string;
    public function getImage(): ?ImageContract;
    public function getLink(): ?HyperlinkContract;
    public function getLinkLabel(): ?string;
    public function getDisplayHint(): ?string;
    public function canPaginate(): bool;
    public function getTeasers(): ?Collection;
}
