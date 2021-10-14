<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Illuminate\Support\Collection;

interface VideoContract extends ContentContract
{
    public function getEmbedUrl(): ?string;

    public function getName(): ?string;

    public function getDescription(): ?string;

    public function getThumbnailUrl(): ?string;

    public function getUploadDate(): ?string;

    public function getContentUrl(): ?string;

    public function getIncludeIntroVideo(): bool;

    public function getDuration(): ?string;

    public function getCaption(): ?string;

    public function getChapterItems(): Collection;
}