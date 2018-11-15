<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
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
        if ($image = array_get($this->acfArray, 'image')) {
            $postMeta = get_post_meta(array_get($image, 'ID'));
            $attachmentMeta = wp_get_attachment_metadata(array_get($image, 'ID'));
            return new Image(new ImageAdapter($image, $postMeta, $attachmentMeta));
        }

        return null;
    }

    public function getImagePosition(): ?string
    {
        return array_get($this->acfArray, 'image_position') ?: null;
    }
}
