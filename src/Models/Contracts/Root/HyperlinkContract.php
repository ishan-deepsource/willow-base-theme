<?php

namespace Bonnier\Willow\Base\Models\Contracts\Root;

interface HyperlinkContract
{
    public function getTitle(): ?string;

    public function getUrl(): ?string;

    public function getRelationship(): ?string;

    public function getTarget(): ?string;
}
