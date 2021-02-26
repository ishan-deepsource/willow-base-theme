<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentFileContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Transformers\Api\Root\FileTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use League\Fractal\TransformerAbstract;

/**
 * Class FileTransformer
 *
 * @package \Bonnier\Willow\Base\Transformers\Api\Composites\Partials
 */
class ContentFileTransformer extends TransformerAbstract
{
    public function transform(ContentFileContract $file)
    {
        return [
            'title' => $file->isLocked() ? null : $file->getTitle(),
            'description' => $file->isLocked() ? null : $file->getDescription(),
            'file'  => $file->isLocked() ? null : (new FileTransformer())->transform($file->getFile()),
            'images' => $file->isLocked() ? [] : $this->getImages($file),
            'download_button_text' => $file->isLocked() ? null : $file->getDownloadButtonText(),
        ];
    }

    private function getImages(ContentFileContract $file)
    {
        return collect($file->getImages())->transform(function (ImageContract $image) {
            return (new ImageTransformer())->transform($image);
        });
    }
}
