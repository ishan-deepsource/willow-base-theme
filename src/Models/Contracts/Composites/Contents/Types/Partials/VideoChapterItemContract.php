<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

interface VideoChapterItemContract
{
    public function getThumbnail(): ?ImageContract;

    public function getTitle(): ?string;

    public function getDescription(): ?string;

    public function getSeconds(): ?int;

    public function getUrl(): ?string;

    public function getShowInListOverview(): ?bool;
}
