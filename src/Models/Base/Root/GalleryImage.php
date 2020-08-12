<?php

namespace Bonnier\Willow\Base\Models\Base\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\GalleryImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

class GalleryImage implements GalleryImageContract
{
    protected $galleryImage;

    public function __construct(GalleryImageContract $galleryImage)
    {
        $this->galleryImage = $galleryImage;
    }

    public function getTitle(): ?string
    {
        return $this->galleryImage->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->galleryImage->getDescription();
    }

    public function getImage(): ?ImageContract
    {
        return $this->galleryImage->getImage();
    }

    public function getVideoUrl(): ?string
    {
        return $this->galleryImage->getVideoUrl();
    }
}
