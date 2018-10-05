<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials\HotspotItemItemAdapter;
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
        $image = array_get($this->acfArray, 'image');
        return $image ? new Image(new ImageAdapter($image)) : null;
    }

    public function getHotspots(): Collection
    {
        return collect($this->acfArray['hotspots'])->transform(function ($acfHotspotArr) {
            return new HotspotItem(new HotspotItemItemAdapter($acfHotspotArr));
        });
    }

    public function getType(): string
    {
        return 'hotspot_image';
    }

    public function isLocked(): bool
    {
        return false;
    }

    public function getStickToNext(): bool
    {
        return false;
    }
}
