<?php

namespace Bonnier\Willow\Base\Models\Base\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Pages\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\AuthorOverviewContract;
use Illuminate\Support\Collection;

/**
 * Class TeaserList
 * @package Bonnier\Willow\Base\Models\Base\Pages\Contents\Types
 * @property AuthorOverviewContract $model
 */
class AuthorOverview extends AbstractContent implements AuthorOverviewContract
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

    public function getAuthors(): Collection
    {
        return $this->model->getAuthors();
    }
}
