<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\VideoChapterItemContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

class VideoChapterItem implements VideoChapterItemContract
{
    private $item;

    public function __construct(VideoChapterItemContract $item)
    {
        $this->item = $item;
    }

    public function getThumbnail(): ?ImageContract
    {
        return $this->item->getThumbnail();
    }

    public function getTitle(): ?string
    {
        return $this->item->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->item->getDescription();
    }

    public function getSeconds(): int
    {
        return $this->item->getSeconds();
    }

    public function getUrl(): ?string
    {
        return $this->item->getUrl();
    }

    public function getShowInListOverview(): ?bool
    {
        return $this->item->getShowInListOverview();
    }

    public function isEmpty(): bool
    {
        return is_null($this->item->getThumbnail())
            && is_null($this->item->getTitle())
            && is_null($this->item->getDescription())
            && $this->item->getSeconds() > 0
            && is_null($this->item->getUrl());
    }
}