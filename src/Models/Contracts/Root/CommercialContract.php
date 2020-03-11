<?php

namespace Bonnier\Willow\Base\Models\Contracts\Root;

interface CommercialContract
{
    public function getType(): ?string;

    public function getLabel(): ?string;

    public function getLogo(): ?ImageContract;

    public function getLinkLabel(): ?string;

    public function getLink(): ?string;
}
