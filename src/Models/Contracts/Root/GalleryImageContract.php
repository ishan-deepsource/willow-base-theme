<?php

namespace Bonnier\Willow\Base\Models\Contracts\Root;

interface GalleryImageContract
{
    public function getDescription(): ?string;

    public function getImage(): ?ImageContract;
}
