<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\GalleryContract;
use Illuminate\Support\Collection;

/**
 * Class Gallery
 *
 * @property GalleryContract $model
 *
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 */
class Gallery extends AbstractContent implements GalleryContract
{
    /**
     * Gallery constructor.
     *
     * @param \Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\GalleryContract $gallery
     */
    public function __construct(GalleryContract $gallery)
    {
        parent::__construct($gallery);
    }

    public function getImages(): Collection
    {
        return $this->model->getImages();
    }
    
    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }
    
    public function getTitle(): ?string
    {
        return $this->model->getTitle();
    }
}
