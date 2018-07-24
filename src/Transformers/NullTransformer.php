<?php

namespace Bonnier\Willow\Base\Transformers;

use League\Fractal\TransformerAbstract;

/**
 * Class NullTransformer
 *
 * @package \Bonnier\Willow\Base\Transformers
 */
class NullTransformer extends TransformerAbstract
{
    public function transform($object)
    {
        return [];
    }
}
