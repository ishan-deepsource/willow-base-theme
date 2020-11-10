<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\MultimediaContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

/**
 * Class Multimedia
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 * @property MultimediaContract $model
 */
class Multimedia extends AbstractContent implements MultimediaContract
{
    public function __construct(MultimediaContract $content)
    {
        parent::__construct($content);
    }

    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }

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

    public function getDisplayHint(): ?string
    {
        return $this->model->getDisplayHint();
    }

    public function getVectaryId(): ?string
    {
        return $this->model->getVectaryId();
    }

    public function getVectaryUrl(): ?string
    {
        return $this->model->getVectaryUrl();
    }
}
