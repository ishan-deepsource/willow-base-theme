<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\StoryContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;
use Bonnier\Willow\Base\Transformers\Api\Terms\Vocabulary\VocabularyTransformer;
use League\Fractal\TransformerAbstract;

class StoryTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'teaser',
        'articles',
        'vocabularies',
    ];

    protected $defaultIncludes = [
        'teaser',
        'articles',
    ];

    public function transform(StoryContract $story)
    {
        return [];
    }

    public function includeTeaser(StoryContract $story)
    {
        return $this->item($story->getTeaser(), new CompositeTeaserTransformer);
    }

    public function includeArticles(StoryContract $story)
    {
        return $this->collection($story->getArticles(), new CompositeTeaserTransformer);
    }

    public function includeVocabularies(StoryContract $story)
    {
        return $this->collection($story->getTeaser()->getVocabularies(), new VocabularyTransformer());
    }
}
