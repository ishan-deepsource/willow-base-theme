<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;


use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Root\GalleryImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

class GalleryImageAdapter implements GalleryImageContract
{
    /** @var array */
    protected $galleryImage;

    /**
     * GalleryImageAdapter constructor.
     *
     * @param array $galleryImage
     */
    public function __construct(array $galleryImage)
    {
        $this->galleryImage = $galleryImage;
    }


    public function getDescription(): ?string
    {
        return $this->galleryImage['description'] ?? null;
    }

    public function getImage(): ?ImageContract
    {
        if (($imageId = $this->galleryImage['image'] ?? null) && $image = get_post($imageId)) {
            return new Image(new ImageAdapter($image));
        }

        return null;
    }
}
