<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeTranslationContract;
use League\Fractal\TransformerAbstract;

class CompositeTranslationTransformer extends TransformerAbstract
{
    public function transform(CompositeTranslationContract $compositeTranslation)
    {
        return [
            'id' => $compositeTranslation->getId(),
            'title' => $compositeTranslation->getTitle(),
            'link' => $compositeTranslation->getLink(),
        ];
    }
}
