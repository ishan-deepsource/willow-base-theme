<?php

namespace Bonnier\Willow\Base\Models\Contracts\Root;

interface CommercialContract
{
    public function getType(): ?string;

    public function getLabel(): ?string;

    public function getLogo(): ?ImageContract;
}
