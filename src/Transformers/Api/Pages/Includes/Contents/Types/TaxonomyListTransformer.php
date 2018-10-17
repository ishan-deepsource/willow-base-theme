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
    protected $taxonomyTransformerMap = [
        Category::class => CategoryTransformer::class,
        Tag::class => TagTransformer::class,
        Vocabulary::class => VocabularyTransformer::class,
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
            'taxonomy_list' => $this->transformList($taxonomyList),
        ];
    }

    private function transformImage(TaxonomyListContract $taxonomyList)
    {
        if ($image = $taxonomyList->getImage()) {
            return with(new ImageTransformer)->transform($image);
        }

        return null;
    }

    private function transformList(TaxonomyListContract $taxonomyList)
    {
        if ($taxonomies = $taxonomyList->getTaxonomyList()) {
            return $taxonomies->map(function ($taxonomy) {
                $transformer = collect($this->taxonomyTransformerMap)
                    ->get(get_class($taxonomy), NullTransformer::class);
                return with(new $transformer)->transform($taxonomy);
            });
        }

        return null;
    }
}
