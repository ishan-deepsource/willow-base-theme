<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentFileContract;
use Bonnier\Willow\Base\Models\Contracts\Root\FileContract;
use Illuminate\Support\Collection;

/**
 * Class File
 *
 * @property ContentFileContract $model
 *
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 */
class ContentFile extends AbstractContent implements ContentFileContract
{

    public function getTitle(): ?string
    {
        return $this->model->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->model->getDescription();
    }

    public function getFile(): ?FileContract
    {
        return $this->model->getFile();
    }

    public function getImages(): Collection
    {
        return $this->model->getImages();
    }

    public function getDownloadButtonText(): ?string
    {
        return $this->model->getDownloadButtonText();
    }

    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }
}
