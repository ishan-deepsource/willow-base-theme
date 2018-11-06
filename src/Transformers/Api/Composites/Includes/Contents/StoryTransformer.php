<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\StoryContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;
use League\Fractal\TransformerAbstract;

class StoryTransformer extends TransformerAbstract
{
    public function transform(StoryContract $story)
    {
        return [
            'teaser' => $this->transformTeaser($story),
            'articles' => $this->transformArticles($story),
        ];
    }

    private function transformTeaser(StoryContract $story)
    {
        return with(new CompositeTeaserTransformer)->transform($story->getTeaser());
    }

    private function transformArticles(StoryContract $story)
    {
        if ($articles = $story->getArticles()) {
            return $articles->map(function (CompositeContract $composite) {
                return with(new CompositeTeaserTransformer)->transform($composite);
            });
        }

        return null;
    }
}
