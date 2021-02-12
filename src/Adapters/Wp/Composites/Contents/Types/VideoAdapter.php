<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials\VideoChapterItemAdapter;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials\VideoChapterItem;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\VideoContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Illuminate\Support\Collection;

/**
 * Class VideoAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class VideoAdapter extends AbstractContentAdapter implements VideoContract
{
    public function getEmbedUrl(): ?string
    {
        if ($embedUrl = array_get($this->acfArray, 'embed_url')) {
            if (preg_match('/src=["\']([^\'"]+)/', $embedUrl, $matches)) {
                return $matches[1];
            }
            return $embedUrl;
        }

        return null;
    }

    public function getCaption(): ?string
    {
        return array_get($this->acfArray, 'caption') ?: null;
    }

    public function getChapterItems(): Collection
    {
        $arr = array_get($this->acfArray, 'chapter_items', []);
        return collect($arr)->map(function ($item) {
            return new VideoChapterItem(new VideoChapterItemAdapter($item));
        })->reject(function (VideoChapterItem $item) {
            return $item->isEmpty();
        });
    }
}
