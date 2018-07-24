<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\InfoBoxContract;
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
        return [
            'title' => $infoBox->isLocked() ? null : $infoBox->getTitle(),
            'body' => $infoBox->isLocked() ? null : $infoBox->getBody(),
        ];
    }
}
