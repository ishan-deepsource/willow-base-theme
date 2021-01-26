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
    public function getEditorsDescriptionTitle(): ?string
    {
        return $this->model->getEditorsDescriptionTitle();
    }

    public function getEditorsDescription(): ?string
    {
        return $this->model->getEditorsDescription();
    }

    public function getAuthors(): Collection
    {
        return $this->model->getAuthors();
    }
}
