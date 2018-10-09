<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentAudioContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Illuminate\Support\Collection;

/**
 * Class File
 *
 * @property ContentAudioContract $model
 *
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 */
class ContentAudio extends AbstractContent implements ContentAudioContract
{
    public function getId(): int
    {
        return $this->model->getId();
    }

    public function getCaption(): ?string
    {
        return $this->model->getCaption();
    }

    public function getUrl(): ?string
    {
        return $this->model->getUrl();
    }

    public function getTitle(): ?string
    {
        return $this->model->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->model->getDescription();
    }

    public function getLanguage(): ?string
    {
        return $this->model->getLanguage();
    }

    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }

    public function getAudioTitle(): ?string
    {
        return $this->model->getAudioTitle();
    }

    public function getImage(): ?ImageContract
    {
        return $this->model->getImage();
    }

    public function getDuration(): int
    {
        return $this->model->getDuration();
    }
}
