<?php

namespace Bonnier\Willow\Base\Transformers\Api\Terms\Category;

use Bonnier\Willow\Base\Exceptions\Controllers\Api\OverrideModelMissingContractException;
use Bonnier\Willow\Base\Factories\WPModelFactory;
use Bonnier\Willow\Base\Helpers\Cache;
use Bonnier\Willow\Base\Models\Base\Terms\Category;
use Bonnier\Willow\Base\Models\Contracts\Root\TranslationContract;
use Bonnier\Willow\Base\Models\Contracts\Terms\CategoryContract;
use Bonnier\Willow\Base\Models\Contracts\Terms\CategoryTranslationContract;
use Bonnier\Willow\Base\Traits\UrlTrait;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\Contents\ContentTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\TeaserTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\TranslationTransformer;
use Bonnier\Willow\Base\Transformers\Api\Terms\Category\Partials\CategoryDetailsTransformer;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    use UrlTrait;

    protected $originalResponseData;

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'details',
        'children',
        'teasers',
        'content-teasers',
        'siblings',
        'parent',
        'contents',
        'ancestor',
    ];

    /**
     * CategoryTransformer constructor.
     *
     * @param array $originalResponseData
     */
    public function __construct(array $originalResponseData = [])
    {
        $this->originalResponseData = $originalResponseData;
    }

    public function transform(CategoryContract $category)
    {
        return [
            'id'            => $category->getId(),
            'name'          => $category->getName(),
            'url'           => $this->getPath($category->getUrl()),
            'language'      => $category->getLanguage(),
            'count'         => $category->getCount(),
            'canonical_url' => $category->getCanonicalUrl(),
            'translations'  => $this->getTranslations($category),
            'contenthub_id' => $category->getContenthubId(),
        ];
    }

    public function includeDetails(CategoryContract $category)
    {
        return $this->item($category, new CategoryDetailsTransformer());
    }

    public function includeTeasers(CategoryContract $category)
    {
        return $this->collection($category->getTeasers(), new TeaserTransformer());
    }

    public function includeChildren(CategoryContract $category)
    {
        return $this->collection($category->getChildren(), new CategoryTransformer());
    }

    public function includeContentTeasers(CategoryContract $category, ParamBag $paramBag)
    {
        list($perPage) = $paramBag->get('per_page') ?: [10];
        list($page) = $paramBag->get('page') ?: [1];
        list($orderby) = $paramBag->get('orderby') ?: ['date'];
        list($order) = $paramBag->get('order') ?: ['DESC'];
        list($offset) = $paramBag->get('offset') ?: ['0'];

        return $this->collection(
            $category->getContentTeasers($page, $perPage, $orderby, $order, $offset),
            new CompositeTeaserTransformer()
        );
    }

    public function includeParent(CategoryContract $category)
    {
        if ($parent = $category->getParent()) {
            return $this->item($parent, new CategoryTransformer());
        }
        return null;
    }

    public function includeAncestor(CategoryContract $category)
    {
        if ($ancestor = $category->getAncestor()) {
            return $this->item($ancestor, new CategoryTransformer);
        }

        return null;
    }

    public function includeSiblings(CategoryContract $category)
    {
        return Cache::remember(
            'willow_sibling_categories-' . $category->getId(),
            4 * HOUR_IN_SECONDS,
            function () use ($category) {
                $categories = get_categories([
                    'orderby' => 'name',
                    'parent' => 0
                ]);
                $prevCat = null;
                $nextCat = null;
                /**
                 * @var int $i
                 * @var \WP_Term $cat
                 */
                foreach ($categories as $i => $cat) {
                    if ($category->getId() == $cat->term_id) {
                        $prevCat = $categories[$i-1] ?? collect($categories)->last();
                        $nextCat = $categories[$i+1] ?? collect($categories)->first();
                        break;
                    }
                }

                $siblings = [
                    'prev' => null,
                    'next' => null,
                ];
                $categoryFactory = new WPModelFactory(Category::class);
                if ($prevCat) {
                    try {
                        $siblings['prev'] = $categoryFactory->getModel($prevCat);
                    } catch (OverrideModelMissingContractException $e) {
                    }
                }
                if ($nextCat) {
                    try {
                        $siblings['next'] = $categoryFactory->getModel($nextCat);
                    } catch (OverrideModelMissingContractException $e) {
                    }
                }
                return $this->collection(
                    collect($siblings)->reject(function ($sibling) {
                        return is_null($sibling);
                    }),
                    new CategoryTransformer()
                );
            }
        );
    }

    public function includeContents(CategoryContract $page)
    {
        return $this->collection($page->getContents(), new ContentTransformer());
    }

    private function getTranslations(CategoryContract $category)
    {
        if ($translations = $category->getTranslations()) {
            return $translations->mapWithKeys(function (TranslationContract $translation, string $locale) {
                return [$locale => with(new TranslationTransformer)->transform($translation)];
            });
        }

        return null;
    }
}
