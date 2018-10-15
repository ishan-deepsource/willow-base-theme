<?php

namespace Bonnier\Willow\Base\Models\Base\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Pages\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\TeaserListContract;
use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Illuminate\Support\Collection;

/**
 * Class TeaserList
 * @package Bonnier\Willow\Base\Models\Base\Pages\Contents\Types
 * @property TeaserListContract $model
 */
class TeaserList extends AbstractContent implements TeaserListContract
{
    public function getTitle(): ?string
    {
        return $this->model->getTitle();
    }

    public function getLabel(): ?string
    {
        return $this->model->getLabel();
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

    public function getLinkLabel(): ?string
    {
        return $this->model->getLinkLabel();
    }

    public function getDisplayHint(): ?string
    {
        return $this->model->getDisplayHint();
    }

    public function getTeasers(): ?Collection
    {
        return $this->model->getTeasers();
    }
}
