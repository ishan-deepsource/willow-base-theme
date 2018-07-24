<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\FileContract;
use Bonnier\Willow\Base\Traits\UrlTrait;
use League\Fractal\TransformerAbstract;

/**
 * Class FileTransformer
 *
 * @package \Bonnier\Willow\Base\Transformers\Api\Composites\Partials
 */
class FileTransformer extends TransformerAbstract
{
    use UrlTrait;

    public function transform(FileContract $file)
    {
        return [
            'id' => $file->getId(),
            'url' => $this->getPath($file->getUrl()),
            'title' => $file->getTitle(),
            'description' => $file->getDescription(),
            'caption' => $file->getCaption(),
            'language' => $file->getLanguage(),
        ];
    }
}
