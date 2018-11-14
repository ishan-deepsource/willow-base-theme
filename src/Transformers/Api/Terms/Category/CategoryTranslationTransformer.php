<?php

namespace Bonnier\Willow\Base\Transformers\Api\Terms\Category;

use Bonnier\Willow\Base\Models\Contracts\Terms\CategoryTranslationContract;
use League\Fractal\TransformerAbstract;

class CategoryTranslationTransformer extends TransformerAbstract
{
    public function transform(CategoryTranslationContract $categoryTranslation)
    {
        return [
            'id' => $categoryTranslation->getId(),
            'title' => $categoryTranslation->getTitle(),
            'link' => $categoryTranslation->getLink(),
        ];
    }
}
