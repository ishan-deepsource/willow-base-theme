<?php

namespace Bonnier\Willow\Base\Models\Base\Terms;

use Bonnier\Willow\Base\Models\Contracts\Terms\TagContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;
use Illuminate\Support\Collection;

class Tag implements TagContract
{
    protected $tag;

    public function __construct(TagContract $tag)
    {
        $this->tag = $tag;
    }

    public function getId(): ?int
    {
        return $this->tag->getId();
    }

    public function getName(): ?string
    {
        return $this->tag->getName();
    }

    public function getUrl(): ?string
    {
        return $this->tag->getUrl();
    }

    public function getLanguage(): ?string
    {
        return $this->tag->getLanguage();
    }

    public function getContentTeasers($page, $perPage, $orderBy, $order): Collection
    {
        return $this->tag->getContentTeasers($page, $perPage, $orderBy, $order);
    }

    public function getCount(): ?int
    {
        return $this->tag->getCount();
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->tag->getCanonicalUrl();
    }

    public function getSlug(): ?string
    {
        return $this->tag->getSlug();
    }

    public function getTeaser(string $type): ?TeaserContract
    {
        return $this->tag->getTeaser($type);
    }

    public function getTeasers(): ?Collection
    {
        return $this->tag->getTeasers();
    }
    
    public function getContents(int $page = 1): ?Collection
    {
        return $this->tag->getContents($page);
    }

    public function getTranslations(): ?Collection
    {
        return $this->tag->getTranslations();
    }

    public function getContenthubId(): ?string
    {
        return $this->tag->getContenthubId();
    }
}
