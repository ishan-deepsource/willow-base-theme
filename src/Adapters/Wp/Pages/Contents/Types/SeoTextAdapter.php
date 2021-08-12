<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\FileAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\SeoTextContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

class SeoTextAdapter extends AbstractContentAdapter implements SeoTextContract
{
    public function getTitle(): ?string
    {
        return array_get($this->acfArray, 'title') ?: null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->acfArray, 'description') ?: null;
    }

    public function getImage(): ?ImageContract
    {
        if ($imageArray = array_get($this->acfArray, 'image')) {
            $image = WpModelRepository::instance()->getPost($imageArray);
            return new Image(new ImageAdapter($image));
        }

        return null;
    }

    public function getImagePosition(): ?string
    {
        return array_get($this->acfArray, 'image_position') ?: null;
    }

    public function getLink(): ?string
    {
        return array_get($this->acfArray, 'link') ?: null;
    }

    public function getLinkTarget(): ?string
    {
        return array_get($this->acfArray, 'link_target') ?: null;
    }

    public function getLinkRel(): ?string
    {
        return array_get($this->acfArray, 'link_rel') ?: null;
    }
}
