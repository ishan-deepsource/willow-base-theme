<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\VideoChapterItemContract;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use League\Fractal\TransformerAbstract;

class VideoChapterItemTransformer extends TransformerAbstract
{
    public function transform(VideoChapterItemContract $videoChapterItem)
    {
        return [
            'thumbnail' => $this->transformImage($videoChapterItem),
            'title' => $videoChapterItem->getTitle(),
            'description' => $videoChapterItem->getDescription(),
            'time' => $videoChapterItem->getTime(),
            'url' => $videoChapterItem->getUrl(),
            'show_in_list_overview' => $videoChapterItem->getShowInListOverview(),
        ];
    }

    private function transformImage(VideoChapterItemContract $videoChapterItem)
    {
        if ($image = $videoChapterItem->getThumbnail()) {
            return with(new ImageTransformer)->transform($image);
        }

        return null;
    }
}