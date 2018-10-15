<?php

namespace Bonnier\Willow\Base\Models\Base\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Pages\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\TaxonomyListContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Illuminate\Support\Collection;

/**
 * Class TaxonomyList
 * @package Bonnier\Willow\Base\Models\Base\Pages\Contents\Types
 * @property TaxonomyListContract $model
 */
class TaxonomyList extends AbstractContent implements TaxonomyListContract
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

    public function getLabel(): ?string
    {
        return $this->model->getLabel();
    }

    public function getDisplayHint(): ?string
    {
        return $this->model->getDisplayHint();
    }

    public function getTaxonomy(): ?string
    {
        return $this->model->getTaxonomy();
    }

    public function getTaxonomyList(): ?Collection
    {
        return $this->model->getTaxonomyList();
    }
}
