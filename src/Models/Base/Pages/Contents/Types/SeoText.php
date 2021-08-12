<?php

namespace Bonnier\Willow\Base\Models\Base\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Pages\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\SeoTextContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

/**
 * Class SeoText
 * @package Bonnier\Willow\Base\Models\Base\Pages\Contents\Types
 * @property SeoTextContract $model
 */
class SeoText extends AbstractContent implements SeoTextContract
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

    public function getImagePosition(): ?string
    {
        return $this->model->getImagePosition();
    }

    public function getLink(): ?string
    {
        return $this->model->getLink();
    }

    public function getLinkTarget(): ?string
    {
        return $this->model->getLinkTarget();
    }

    public function getLinkRel(): ?string
    {
        return $this->model->getLinkRel();
    }
}
