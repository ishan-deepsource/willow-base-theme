<?php

namespace Bonnier\Willow\Base\Models\Contracts\Root;

interface TeaserContract
{
    public function getTitle(): ?string;

    public function getImage(): ?ImageContract;

    public function getVideoUrl(): ?string;

    public function getDescription(): ?string;

    public function getType(): ?string;
}
