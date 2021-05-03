<?php

namespace Bonnier\Willow\Base\Helpers;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\VideoContract;

class VideoMetaHelper
{
    public function addToOutput(CompositeContract $composite, array &$out) : void
    {
        if (in_array($composite->getTemplate(), ['workout-video', 'exercise-video'])) {
            collect($composite->getContents())->each(function($content) use(&$out) {
                if ($content->getType() == 'video') {
                    $out['video_meta'] = $this->getVideoMeta($content);
                    return false;
                }
            });
        }
    }

    private function getVideoMeta(VideoContract $video)
    {
        return [
            'duration' => $video->getDuration()
        ];
    }
}
