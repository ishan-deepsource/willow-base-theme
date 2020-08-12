<?php

namespace Bonnier\Willow\Base\Models\Contracts\Root;

interface GalleryImageContract
{
    public function getTitle(): ?string;

    public function getDescription(): ?string;

    public function getImage(): ?ImageContract;

    public function getVideoUrl(): ?string;
}
