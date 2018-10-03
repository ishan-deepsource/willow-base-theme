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
    public function __construct(TeaserListContract $teaserList)
    {
        parent::__construct($teaserList);
    }

    public function getTitle(): ?string
    {
        return $this->model->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->model->getDescription();
    }

    public function getBackgroundImage(): ?ImageContract
    {
        return $this->model->getBackgroundImage();
    }

    public function getLink(): ?HyperlinkContract
    {
        return $this->model->getLink();
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
