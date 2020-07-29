<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials\ParagraphListItemAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials\ParagraphListItem;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ParagraphListContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\ParagraphListItemContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Illuminate\Support\Collection;

class ParagraphListAdapter extends AbstractContentAdapter implements ParagraphListContract
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

    public function isCollapsible(): bool
    {
        return boolval(array_get($this->acfArray, 'collapsible', false));
    }

    public function getDisplayHint(): ?string
    {
        return array_get($this->acfArray, 'display_hint') ?: null;
    }

    public function getItems(): Collection
    {
        return collect(array_get($this->acfArray, 'items', []))->map(function ($item) {
            return new ParagraphListItem(new ParagraphListItemAdapter($item));
        })->reject(function (ParagraphListItemContract $item) {
            return is_null($item->getDescription()) &&
                is_null($item->getImage()) &&
                is_null($item->getTitle());
        });
    }

	public function getVideoUrl(): ?string
	{
		return array_get($this->acfArray, CompositeFieldGroup::VIDEO_URL_FIELD_NAME) ?: null;
	}
}
