<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\TranslationContract;
use League\Fractal\TransformerAbstract;

class TranslationTransformer extends TransformerAbstract
{
    public function transform(TranslationContract $translation)
    {
        return [
            'id' => $translation->getId(),
            'title' => $translation->getTitle(),
            'link' => $translation->getLink(),
        ];
    }
}
