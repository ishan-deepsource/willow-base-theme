<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\NativeVideoContract;
use Bonnier\Willow\Base\Traits\UrlTrait;
use League\Fractal\TransformerAbstract;

/**
 * Class NativeVideoTransformer
 *
 * @package \Bonnier\Willow\Base\Transformers\Api\Composites\Partials
 */
class NativeVideoTransformer extends TransformerAbstract
{
    use UrlTrait;

    public function transform(NativeVideoContract $nativeVideo)
    {
        return [
            'id' => $nativeVideo->getId(),
            'url' => $this->getPath($nativeVideo->getUrl()),
            'title' => $nativeVideo->getTitle(),
            'description' => $nativeVideo->getDescription(),
            'caption' => $nativeVideo->getCaption(),
            'language' => $nativeVideo->getLanguage(),
        ];
    }
}
