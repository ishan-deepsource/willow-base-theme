<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\VideoChapterItemContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;

class VideoChapterItemAdapter implements VideoChapterItemContract
{
    private $item;

    public function __construct($item)
    {
        $this->item = $item;
    }

    public function getThumbnail(): ?ImageContract
    {
        if ($imageArray = array_get($this->item, 'thumbnail')) {
            $image = WpModelRepository::instance()->getPost($imageArray);
            return new Image(new ImageAdapter($image));
        }

        return null;
    }

    public function getTitle(): ?string
    {
        return array_get($this->item, 'title') ?: null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->item, 'description') ?: null;
    }

    public function getSeconds(): int
    {
        return array_get($this->item, 'seconds') ?: 0;
    }

    public function getUrl(): ?string
    {
        return array_get($this->item, 'url') ?: null;
    }

    public function getShowInListOverview(): ?bool
    {
        return boolval(array_get($this->item, 'show_in_list_overview', false));
    }
}