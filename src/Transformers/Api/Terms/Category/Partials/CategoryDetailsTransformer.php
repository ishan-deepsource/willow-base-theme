<?php

namespace Bonnier\Willow\Base\Transformers\Api\Terms\Category\Partials;

use Bonnier\Willow\Base\Models\Contracts\Terms\CategoryContract;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use League\Fractal\TransformerAbstract;

class CategoryDetailsTransformer extends TransformerAbstract
{
    public function transform(CategoryContract $category)
    {
        return [
            'description' => $category->getDescription() ?? '',
            'image' => with(new ImageTransformer())->transform($category->getImage()),
            'body' => $category->getBody() ?? '',
        ];
    }
}
