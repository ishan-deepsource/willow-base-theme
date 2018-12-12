<?php

namespace Bonnier\Willow\Base\Models\Base\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Pages\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\CommercialSpotContract;
use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

/**
 * Class TeaserList
 * @package Bonnier\Willow\Base\Models\Base\Pages\Contents\Types
 * @property CommercialSpot $model
 */
class CommercialSpot extends AbstractContent implements CommercialSpotContract
{
    public function getTitle(): ?string
    {
        return $this->model->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->model->getDescription();
    }

    public function getImage(): ?ImageContract
    {
        return $this->model->getImage();
    }

    public function getLink(): ?HyperlinkContract
    {
        return $this->model->getLink();
    }
}
