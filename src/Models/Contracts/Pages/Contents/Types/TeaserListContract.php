<?php

namespace Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Illuminate\Support\Collection;

interface TeaserListContract extends ContentContract
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
    public function setPage(int $page): TeaserListContract;
    public function getPage(): int;
    public function getTotalTeasers(): ?int;
    public function getTotalPages(): ?int;
    public function getTeasersPerPage(): ?int;
    public function getNextCursor(): ?string;
    public function getPreviousCursor(): ?string;
    public function getCurrentCursor(): ?string;
    public function setParentId(int $parentId): ?TeaserListContract;
}
