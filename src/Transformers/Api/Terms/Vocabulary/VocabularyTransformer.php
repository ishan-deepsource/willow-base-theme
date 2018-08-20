<?php

namespace Bonnier\Willow\Base\Transformers\Api\Terms\Vocabulary;

use Bonnier\Willow\Base\Models\Contracts\Terms\TagContract;
use Bonnier\Willow\Base\Models\Contracts\Terms\VocabularyContract;
use Bonnier\Willow\Base\Traits\UrlTrait;
use Bonnier\Willow\Base\Transformers\Api\Terms\Tag\TagTransformer;
use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;

class VocabularyTransformer extends TransformerAbstract
{
    use UrlTrait;

    protected $originalResponseData;


    public function __construct(array $originalResponseData = [])
    {
        $this->originalResponseData = $originalResponseData;
    }

    public function transform(VocabularyContract $vocabulary)
    {
        return [
            'name' => $vocabulary->getName(),
            'taxonomy' => $vocabulary->getMachineName(),
            'multi_select' => $vocabulary->getMultiSelect() === '' ? false : true,
            'terms' => $this->transformTerms($vocabulary->getTerms())->toArray(),
        ];
    }

    private function transformTerms(Collection $tags) {
        return collect($tags)->map(function(TagContract $tag){
            return with(new TagTransformer())->transform($tag);
        });
    }
}
