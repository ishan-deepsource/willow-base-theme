<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials\ParagraphListItemAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials\ParagraphListItem;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ParagraphListContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Illuminate\Support\Collection;

class ParagraphListAdapter extends AbstractContentAdapter implements ParagraphListContract
{
    public function __construct(array $acfArray, \WP_Post $post = null)
    {
        parent::__construct($acfArray, $post);
    }

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
        if (($imageId = array_get($this->acfArray, 'image')) && $image = get_post($imageId)) {
            return new Image(new ImageAdapter($image));
        }

        return null;
    }

    public function getDisplayHint(): string
    {
        return array_get($this->acfArray, 'display_hint') ?: 'default';
    }

    public function getItems(): Collection
    {
        return collect(array_get($this->acfArray, 'items', []))->map(function ($item) {
            return new ParagraphListItem(new ParagraphListItemAdapter($item));
        });
    }
}
