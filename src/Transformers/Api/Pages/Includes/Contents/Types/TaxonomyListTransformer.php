<?php

namespace Bonnier\Willow\Base\Transformers\Api\Pages\Includes\Contents\Types;

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
        Category::class => CategoryTransformer::class,
        Tag::class => TagTransformer::class,
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
        $taxonomyList = $taxonomyList->getTaxonomyList();
        $transformerClass = collect($this->transformerMappping)->get(get_class($taxonomyList->first()), NullTransformer::class);
        if ($taxonomyList && !$taxonomyList->isEmpty()) {
            return $this->collection($taxonomyList, new $transformerClass);
        }
    }
}
