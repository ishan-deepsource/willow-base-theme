<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\VideoContract;
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
            'caption'   => $video->isLocked() ? null : $video->getCaption(),
        ];
    }
}
