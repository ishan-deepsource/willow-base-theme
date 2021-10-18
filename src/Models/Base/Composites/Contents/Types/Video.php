<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\VideoContract;
use Illuminate\Support\Collection;

/**
 * Class Link
 *
 * @property VideoContract $model
 *
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 */
class Video extends AbstractContent implements VideoContract
{
    /**
     * Link constructor.
     *
     * @param \Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\VideoContract $video
     */
    public function __construct(VideoContract $video)
    {
        parent::__construct($video);
    }

    public function getEmbedUrl(): ?string
    {
        return $this->model->getEmbedUrl();
    }

    public function getIncludeIntroVideo(): bool
    {
        return $this->model->getIncludeIntroVideo();
    }

    public function getDuration(): ?string
    {
        return $this->model->getDuration();
    }

    public function getCaption(): ?string
    {
        return $this->model->getCaption();
    }

    public function getChapterItems(): Collection
    {
        return $this->model->getChapterItems();
    }

    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }

    public function getName(): ?string
    {
        return $this->model->getName();
    }

    public function getThumbnailUrl(): ?string
    {
        return $this->model->getThumbnailUrl();
    }

    public function getDescription(): ?string
    {
        return $this->model->getDescription();
    }

    public function getUploadDate(): ?string
    {
        return $this->model->getUploadDate();
    }

    public function getContentUrl(): ?string
    {
        return $this->model->getContentUrl();
    }
}