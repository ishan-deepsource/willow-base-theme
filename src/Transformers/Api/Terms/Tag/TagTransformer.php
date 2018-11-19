<?php

namespace Bonnier\Willow\Base\Transformers\Api\Terms\Tag;

use Bonnier\Willow\Base\Models\Contracts\Root\TranslationContract;
use Bonnier\Willow\Base\Models\Contracts\Terms\TagContract;
use Bonnier\Willow\Base\Traits\UrlTrait;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\TeaserTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\TranslationTransformer;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class TagTransformer extends TransformerAbstract
{
    use UrlTrait;

    protected $originalResponseData;

    protected $availableIncludes = [
        'content-teasers',
        'teasers',
    ];

    public function __construct(array $originalResponseData = [])
    {
        $this->originalResponseData = $originalResponseData;
    }

    public function transform(TagContract $tag)
    {
        return [
            'id'            => $tag->getId(),
            'name'          => $tag->getName(),
            'slug'          => $tag->getSlug(),
            'url'           => $this->getPath($tag->getUrl()),
            'language'      => $tag->getLanguage(),
            'count'         => $tag->getCount(),
            'canonical_url' => $tag->getCanonicalUrl(),
            'translations'  => $this->getTranslations($tag),
            'contenthub_id' => $tag->getContenthubId(),
        ];
    }

    public function includeContentTeasers(TagContract $tag, ParamBag $paramBag)
    {
        list($perPage) = $paramBag->get('per_page') ?: [10];
        list($page) = $paramBag->get('page') ?: [1];
        list($orderby) = $paramBag->get('orderby') ?: ['date'];
        list($order) = $paramBag->get('order') ?: ['DESC'];

        return $this->collection(
            $tag->getContentTeasers($page, $perPage, $orderby, $order),
            new CompositeTeaserTransformer()
        );
    }

    public function includeTeasers(TagContract $tag)
    {
        return $this->collection($tag->getTeasers(), new TeaserTransformer());
    }

    private function getTranslations(TagContract $tag)
    {
        if ($translations = $tag->getTranslations()) {
            return $translations->mapWithKeys(function (TranslationContract $translation, string $locale) {
                return [$locale => with(new TranslationTransformer)->transform($translation)];
            });
        }

        return null;
    }
}
