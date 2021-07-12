<?php

namespace Bonnier\Willow\Base\Transformers\Api\Terms\Tag\Partials;

use Bonnier\Willow\Base\Models\Contracts\Terms\TagContract;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use League\Fractal\TransformerAbstract;

class TagDetailsTransformer extends TransformerAbstract
{
    public function transform(TagContract $tag){
        return [
            'description' => $tag->getDescription() ?? '',
            'image' => with(new ImageTransformer())->transform($tag->getImage()),
        ];
    }
}