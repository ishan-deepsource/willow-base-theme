<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\InsertedCodeContract;
use League\Fractal\TransformerAbstract;

class InsertedCodeTransformer extends TransformerAbstract
{
    public function transform(InsertedCodeContract $insertedCode)
    {
        return [
            'code' => $insertedCode->isLocked() ? null : $insertedCode->getCode()
        ];
    }
}
