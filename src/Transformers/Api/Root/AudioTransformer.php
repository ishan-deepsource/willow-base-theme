<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\AudioContract;
use Bonnier\Willow\Base\Traits\UrlTrait;
use League\Fractal\TransformerAbstract;

/**
 * Class FileTransformer
 *
 * @package \Bonnier\Willow\Base\Transformers\Api\Composites\Partials
 */
class AudioTransformer extends TransformerAbstract
{
    use UrlTrait;

    public function transform(AudioContract $audio)
    {
        return [
            'id' => $audio->getId(),
            'url' => $this->getPath($audio->getUrl()),
            'title' => $audio->getTitle(),
            'description' => $audio->getDescription(),
            'caption' => $audio->getCaption(),
            'language' => $audio->getLanguage(),
        ];
    }
}
