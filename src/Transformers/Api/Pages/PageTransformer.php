<?php

namespace Bonnier\Willow\Base\Transformers\Api\Pages;

use Bonnier\Willow\Base\Models\Contracts\Pages\PageContract;
use Bonnier\Willow\Base\Transformers\Api\Root\AuthorTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\Contents\ContentTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\TeaserTransformer;
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
            'published_at'  => $page->getPublishedAt(),
            'updated_at'    => $page->getUpdatedAt(),
            'is_front_page' => $page->isFrontPage(),
            'canonical_url' => $page->getCanonicalUrl(),
            'language_urls' => $page->getLanguageUrls(),
        ];
    }

    public function includeTeasers(PageContract $page)
    {
        return $this->collection($page->getTeasers(), new TeaserTransformer());
    }

    public function includeContents(PageContract $page)
    {
        return $this->collection($page->getContents(), new ContentTransformer());
    }

    private function getAuthor(PageContract $page)
    {
        if ($author = $page->getAuthor()) {
            return with(new AuthorTransformer())->transform($author);
        }

        return null;
    }
}
