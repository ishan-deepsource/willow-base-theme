<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentImageContract;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use League\Fractal\TransformerAbstract;

class ContentImageTransformer extends TransformerAbstract
{
    public function transform(ContentImageContract $image)
    {
        return [
            'is_lead' => $image->isLead(),
            'file' => $image->isLocked() ? null : with(new ImageTransformer())->transform($image)
        ];
    }
}
