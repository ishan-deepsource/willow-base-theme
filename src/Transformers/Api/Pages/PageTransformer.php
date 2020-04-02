<?php

namespace Bonnier\Willow\Base\Transformers\Api\Pages;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Pages\PageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TranslationContract;
use Bonnier\Willow\Base\Transformers\Api\Root\AuthorTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\Contents\ContentTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\DateTimeTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\TeaserTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\TranslationTransformer;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class PageTransformer extends TransformerAbstract
{
    protected $originalResponseData;

    protected $availableIncludes = [
        'teasers',
        'contents',
    ];

    public function __construct(array $originalResponseData = [])
    {
        $this->originalResponseData = $originalResponseData;
    }

    public function transform(PageContract $page)
    {
        return [
            'id'            => $page->getId(),
            'title'         => $page->getTitle(),
            'content'       => $page->getContent(),
            'status'        => $page->getStatus(),
            'author'        => $this->getAuthor($page),
            'template'      => $page->getTemplate(),
            'published_at'  => $this->getPublishedAt($page),
            'updated_at'    => $this->getUpdatedAt($page),
            'is_front_page' => $page->isFrontPage(),
            'canonical_url' => $page->getCanonicalUrl(),
            'translations'  => $this->getTranslations($page),
        ];
    }

    public function includeTeasers(PageContract $page)
    {
        return $this->collection($page->getTeasers(), new TeaserTransformer());
    }

    public function includeContents(PageContract $page, ParamBag $paramBag)
    {
        $currentPage = intval($paramBag->get('page')[0]) ?: 1;
        return $this->collection($page->getContents($currentPage), new ContentTransformer());
    }

    private function getAuthor(PageContract $page)
    {
        if ($author = $page->getAuthor()) {
            return with(new AuthorTransformer())->transform($author);
        }

        return null;
    }

    private function getTranslations(PageContract $page)
    {
        if ($translations = $page->getTranslations()) {
            return $translations->mapWithKeys(function (TranslationContract $translation, string $locale) {
                return [$locale => with(new TranslationTransformer)->transform($translation)];
            });
        }

        return null;
    }

    private function getPublishedAt(PageContract $page)
    {
        if ($publishedAt = $page->getPublishedAt()) {
            return with(new DateTimeTransformer())->transform($publishedAt);
        }
        return null;
    }

    private function getUpdatedAt(PageContract $page)
    {
        if ($publishedAt = $page->getPublishedAt()) {
            return with(new DateTimeTransformer())->transform($publishedAt);
        }
        return null;
    }
}
