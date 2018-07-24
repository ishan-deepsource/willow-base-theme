<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\TextItemContract;
use League\Fractal\TransformerAbstract;

/**
 * Class TextItemTransformer
 *
 * @package \Bonnier\Willow\Base\Transformers\Api\Composites\Partials
 */
class TextItemTransformer extends TransformerAbstract
{
    public function transform(TextItemContract $textItem)
    {
        return [
            'body' => $textItem->isLocked() ? null : $textItem->getBody()
        ];
    }
}
