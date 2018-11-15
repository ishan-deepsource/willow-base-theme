<?php

namespace Bonnier\Willow\Base\Models\Base\Pages;

use Bonnier\Willow\Base\Models\Contracts\Pages\PageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;
use DateTime;
use Illuminate\Support\Collection;

class Page implements PageContract
{
    protected $page;

    public function __construct(PageContract $page)
    {
        $this->page = $page;
    }

    public function getId(): int
    {
        return $this->page->getId();
    }

    public function getTitle(): ?string
    {
        return $this->page->getTitle();
    }

    public function getContent(): ?string
    {
        return $this->page->getContent();
    }

    public function getStatus(): ?string
    {
        return $this->page->getStatus();
    }

    public function getAuthor(): ?AuthorContract
    {
        return $this->page->getAuthor();
    }

    public function getTemplate(): ?string
    {
        return $this->page->getTemplate();
    }

    public function getPublishedAt(): ?DateTime
    {
        return $this->page->getPublishedAt();
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->page->getUpdatedAt();
    }

    public function isFrontPage(): bool
    {
        return $this->page->isFrontPage();
    }

    public function getTeaser(string $type): ?TeaserContract
    {
        return $this->page->getTeaser($type);
    }

    public function getTeasers(): ?Collection
    {
        return $this->page->getTeasers();
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->page->getCanonicalUrl();
    }

    public function getContents(): ?Collection
    {
        return $this->page->getContents();
    }

    public function getTranslations(): ?Collection
    {
        return $this->page->getTranslations();
    }
}
