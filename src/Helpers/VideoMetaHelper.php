<?php

namespace Bonnier\Willow\Base\Helpers;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\VideoContract;
use Bonnier\WP\Cxense\WpCxense;

class VideoMetaHelper
{
    public function addToOutput(CompositeContract $composite, array &$out) : void
    {
        if (in_array($composite->getTemplate(), ['workout-video', 'exercise-video'])) {

            $contents = collect($composite->getContents());
            $firstContent = $contents->first();

            if (is_array($firstContent) && isset($firstContent['type']) && $firstContent['type'] === 'cxense') {
                $orgPreFix = WpCxense::instance()->settings->getOrganisationPrefix();
                $out['video_meta'] = [];
                if (isset($firstContent[$orgPreFix . '-video-meta-duration']))
                    $out['video_meta']['duration'] = $firstContent[$orgPreFix . '-video-meta-duration'];
                if (isset($firstContent[$orgPreFix . '-video-meta-workout-time']))
                    $out['video_meta']['duration'] = $firstContent[$orgPreFix . '-video-meta-workout-time'];
                if (isset($firstContent[$orgPreFix . '-video-meta-workout-level']))
                    $out['video_meta']['duration'] = $firstContent[$orgPreFix . '-video-meta-workout-level'];
            }
            else {
                $contents->each(function($content) use(&$out) {
                    if ($content->getType() == 'video') {
                        $out['video_meta'] = $this->getVideoMeta($content);
                        return false;
                    }
                });
            }
        }
    }

    private function getVideoMeta(VideoContract $video)
    {
        return [
            'duration' => $video->getDuration()
        ];
    }
}
