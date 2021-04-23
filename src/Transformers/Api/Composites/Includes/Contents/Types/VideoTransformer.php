<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\VideoChapterItemContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\VideoContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\Partials\VideoChapterItemTransformer;
use League\Fractal\TransformerAbstract;

/**
 * Class VideoTransformer
 *
 * @package \Bonnier\Willow\Base\Transformers\Api\Composites\Partials
 */
class VideoTransformer extends TransformerAbstract
{
    public function transform(VideoContract $video)
    {
        return [
            'embed_url' => $video->isLocked() ? null : $video->getEmbedUrl(),
            'include_intro_video' => $video->isLocked() ? null : $video->getIncludeIntroVideo(),
            'duration' => $video->isLocked() ? null : $video->getDuration(),
            'caption' => $video->isLocked() ? null : $video->getCaption(),
            'chapter_items' => $this->transformChapterItems($video),
        ];
    }

    private function transformChapterItems(VideoContract $video)
    {
        $chapterItems = $video->getChapterItems();
        return $chapterItems
            ->map(function (VideoChapterItemContract $videoChapterItem){
                return with(new VideoChapterItemTransformer())
                    ->transform($videoChapterItem);
            });
    }
}
