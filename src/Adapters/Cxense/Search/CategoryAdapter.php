<?php

namespace Bonnier\Willow\Base\Adapters\Cxense\Search;

use Bonnier\WP\Cxense\Parsers\Document;
use Bonnier\Willow\Base\Adapters\Cxense\Search\Partials\CategoryTeaserAdapter;
use Bonnier\Willow\Base\Models\Base\Root\Teaser;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;
use Bonnier\Willow\Base\Models\Contracts\Terms\CategoryContract;
use Illuminate\Support\Collection;

/**
 * Class CategoryAdapter
 *
 * @package \\${NAMESPACE}
 */
class CategoryAdapter implements CategoryContract
{
    protected $name;
    protected $url;

    public function __construct(Document $document)
    {
        $this->name = $document->getField('bod-taxo-cat');
        $this->url = $document->getField('bod-taxo-cat-url');
    }

    public function getId(): ?int
    {
        return null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getChildren(): ?Collection
    {
        return null;
    }

    public function getImage(): ?ImageContract
    {
        return null;
    }

    public function getDescription(): ?string
    {
        return null;
    }

    public function getLanguage(): ?string
    {
        return null;
    }

    public function getContentTeasers($page, $perPage, $orderBy, $order, $offset): Collection
    {
        return collect();
    }

    public function getCount(): ?int
    {
        return null;
    }

    public function getTeaser(string $type): ?TeaserContract
    {
        return new Teaser(new CategoryTeaserAdapter($this, $type));
    }

    public function getTeasers(): ?Collection
    {
        return collect($this->getTeaser('default'));
    }

    public function getBody(): ?string
    {
        return null;
    }

    public function getParent(): ?CategoryContract
    {
        return null;
    }

    public function getAncestor(): ?CategoryContract
    {
        return null;
    }

    public function getCanonicalUrl(): ?string
    {
        return null;
    }

    public function getTranslations(): ?Collection
    {
        return null;
    }

    public function getContents(): ?Collection
    {
        return null;
    }
}
