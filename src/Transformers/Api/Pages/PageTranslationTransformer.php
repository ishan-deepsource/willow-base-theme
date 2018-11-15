<?php

namespace Bonnier\Willow\Base\Transformers\Api\Pages;

use Bonnier\Willow\Base\Models\Contracts\Pages\PageTranslationContract;
use League\Fractal\TransformerAbstract;

class PageTranslationTransformer extends TransformerAbstract
{
    public function transform(PageTranslationContract $pageTranslation)
    {
        return [
            'id' => $pageTranslation->getId(),
            'title' => $pageTranslation->getTitle(),
            'link' => $pageTranslation->getLink(),
        ];
    }
}
