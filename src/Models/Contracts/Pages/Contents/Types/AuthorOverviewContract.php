<?php

namespace Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Models\Contracts\Utilities\WidgetPaginationContract;
use Illuminate\Support\Collection;

interface AuthorOverviewContract extends ContentContract
{
    public function getEditorsDescription(): ?string;

    public function getAuthors(): Collection;
}
