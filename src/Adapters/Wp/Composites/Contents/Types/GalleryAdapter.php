<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
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
        return $this->acfArray['title'] ?? null;
    }
    
    public function getImages(): Collection
    {
        $collection = collect($this->acfArray['images'] ?? [])->map(function ($acfImage) {
            if ($image = get_post($acfImage['image'] ?? null)) {
                return new Image(new ImageAdapter($image));
            }
            return null;
        })->reject(function ($image) {
            return is_null($image);
        });
        return $collection;
    }
}
