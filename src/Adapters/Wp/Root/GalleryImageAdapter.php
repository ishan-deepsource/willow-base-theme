<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;


use Bonnier\Willow\Base\Repositories\WpModelRepository;
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

    public function getTitle(): ?string
    {
        return array_get($this->galleryImage, 'title') ?: null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->galleryImage, 'description') ?: null;
    }

    public function getImage(): ?ImageContract
    {
        if ($imageArray = array_get($this->galleryImage, 'image')) {
            $image = WpModelRepository::instance()->getPost($imageArray);
            return new Image(new ImageAdapter($image));
        }

        return null;
    }
}
