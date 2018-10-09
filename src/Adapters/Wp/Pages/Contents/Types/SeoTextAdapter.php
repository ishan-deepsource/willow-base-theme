<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\NativeVideoAdapter;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Base\Root\NativeVideo;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\FeaturedContentContract;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\SeoTextContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\NativeVideoContract;
use Bonnier\WP\ContentHub\Editor\Helpers\SortBy;

class SeoTextAdapter extends AbstractContentAdapter implements SeoTextContract
{
    public function getTitle(): ?string
    {
        return array_get($this->acfArray, 'title') ?? null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->acfArray, 'description') ?? null;
    }

    public function getImage(): ?ImageContract
    {
        if (($imageId = array_get($this->acfArray, 'image')) && $image = get_post($imageId)) {
            return new Image(new ImageAdapter($image));
        }

        return null;
    }
}
