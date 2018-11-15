<?php

namespace Bonnier\Willow\Base\Transformers\Api\Terms\Tag;

use Bonnier\Willow\Base\Models\Contracts\Terms\TagTranslationContract;
use League\Fractal\TransformerAbstract;

class TagTranslationTransformer extends TransformerAbstract
{
    public function transform(TagTranslationContract $tagTranslation)
    {
        return [
            'id' => $tagTranslation->getId(),
            'title' => $tagTranslation->getTitle(),
            'link' => $tagTranslation->getLink(),
        ];
    }
}
