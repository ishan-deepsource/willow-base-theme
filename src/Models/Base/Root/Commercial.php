<?php

namespace Bonnier\Willow\Base\Models\Base\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\CommercialContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

class Commercial implements CommercialContract
{
    protected $commercial;

    public function __construct(CommercialContract $commercial)
    {
        $this->commercial = $commercial;
    }

    public function getType(): ?string
    {
        return $this->commercial->getType();
    }

    public function getLabel(): ?string
    {
        return $this->commercial->getLabel();
    }

    public function getLogo(): ?ImageContract
    {
        return $this->commercial->getLogo();
    }
}
