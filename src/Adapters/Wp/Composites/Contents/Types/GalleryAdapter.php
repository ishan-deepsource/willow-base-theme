<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\GalleryImageAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Models\Base\Root\GalleryImage;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\GalleryContract;
use Illuminate\Support\Collection;

/**
 * Class GalleryAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class GalleryAdapter extends AbstractContentAdapter implements GalleryContract
{
    public function getTitle(): ?string
    {
        return array_get($this->acfArray, 'title') ?: null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->acfArray, 'description') ?: null;
    }

    public function getImages(): Collection
    {
        $collection = collect(array_get($this->acfArray, 'images', []))->map(function ($acfImage) {
            return new GalleryImage(new GalleryImageAdapter($acfImage));
        })->reject(function ($image) {
            return is_null($image);
        });
        return $collection;
    }

    public function getDisplayHint(): ?string
    {
        return array_get($this->acfArray, 'display_hint') ?: null;
    }
}
