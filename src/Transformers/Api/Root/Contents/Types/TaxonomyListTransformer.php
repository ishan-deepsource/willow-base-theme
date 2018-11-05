<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Terms\Category;
use Bonnier\Willow\Base\Models\Base\Terms\Tag;
use Bonnier\Willow\Base\Models\Base\Terms\Vocabulary;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\TaxonomyListContract;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use Bonnier\Willow\Base\Transformers\Api\Terms\Category\CategoryTransformer;
use Bonnier\Willow\Base\Transformers\Api\Terms\Tag\TagTransformer;
use Bonnier\Willow\Base\Transformers\Api\Terms\Vocabulary\VocabularyTransformer;
use Bonnier\Willow\Base\Transformers\NullTransformer;
use League\Fractal\TransformerAbstract;

class TaxonomyListTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'taxonomy_list'
    ];

    protected $defaultIncludes = [
        'taxonomy_list'
    ];

    protected $transformerMappping = [
        'category' => CategoryTransformer::class,
        'tag' => TagTransformer::class,
    ];

    public function transform(TaxonomyListContract $taxonomyList)
    {
        return [
            'title' => $taxonomyList->getTitle(),
            'description' => $taxonomyList->getDescription(),
            'image' => $this->transformImage($taxonomyList),
            'label' => $taxonomyList->getLabel(),
            'display_hint' => $taxonomyList->getDisplayHint(),
            'taxonomy' => $taxonomyList->getTaxonomy(),
        ];
    }

    private function transformImage(TaxonomyListContract $taxonomyList)
    {
        if ($image = $taxonomyList->getImage()) {
            return with(new ImageTransformer)->transform($image);
        }

        return null;
    }


    /**
     * @param TaxonomyListContract $taxonomyList
     * @return \League\Fractal\Resource\Collection
     */
    public function includeTaxonomyList(TaxonomyListContract $taxonomyList)
    {
        $transformerClass = collect($this->transformerMappping)->get($taxonomyList->getTaxonomy(), NullTransformer::class);
        if (optional($taxonomyList->getTaxonomyList())->isNotEmpty()) {
            return $this->collection($taxonomyList->getTaxonomyList(), new $transformerClass);
        }
        return null;
    }
}
