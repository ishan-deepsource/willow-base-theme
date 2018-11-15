<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials\HotspotItemAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials\HotspotItem;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\HotspotImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Illuminate\Support\Collection;

/**
 * Class GalleryAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class HotspotImageAdapter extends AbstractContentAdapter implements HotspotImageContract
{
    public function getTitle(): ?string
    {
        return array_get($this->acfArray, 'title') ?: null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->acfArray, 'description') ?: null;
    }

    public function getDisplayHint(): ?string
    {
        return array_get($this->acfArray, 'display_hint') ?: null;
    }

    public function getImage(): ?ImageContract
    {
        if ($image = array_get($this->acfArray, 'image')) {
            $meta = wp_get_attachment_metadata(array_get($image, 'ID'));
            return new Image(new ImageAdapter($image, $meta));
        }
        return null;
    }

    public function getHotspots(): Collection
    {
        return collect(array_get($this->acfArray, 'hotspots', []))->transform(function ($acfHotspotArr) {
            return new HotspotItem(new HotspotItemAdapter($acfHotspotArr));
        });
    }
}
