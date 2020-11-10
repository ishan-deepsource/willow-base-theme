<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\InfoBoxContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\MultimediaContract;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use League\Fractal\TransformerAbstract;

/**
 * Class FileTransformer
 *
 * @package \Bonnier\Willow\Base\Transformers\Api\Composites\Partials
 */
class InfoBoxTransformer extends TransformerAbstract
{
    public function transform(InfoBoxContract $infoBox)
    {
        //var_dump($infoBox);exit;
        return [
            'title' => $infoBox->isLocked() ? null : $infoBox->getTitle(),
            'body' => $infoBox->isLocked() ? null : $infoBox->getBody(),
            'image' => $infoBox->isLocked() ? null : $this->transformImage($infoBox),
            'display_hint' => $infoBox->isLocked() ? null : $infoBox->getDisplayHint(),
        ];
    }

    private function transformImage(InfoBoxContract $infoBox)
    {
        if ($image = $infoBox->getImage()) {
            return with(new ImageTransformer)->transform($image);
        }

        return null;
    }
}
