<?php

namespace Bonnier\Willow\Base\Models\Base\Terms;

use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;
use Bonnier\Willow\Base\Models\Contracts\Terms\CategoryContract;
use Illuminate\Support\Collection;

/**
 * Class Composite
 *
 * @package \Bonnier\Willow\Base\Models
 */
class Category implements CategoryContract
{
    protected $category;

    /**
     * Composite constructor.
     *
     * @param \Bonnier\Willow\Base\Models\Contracts\Terms\CategoryContract $category
     */
    public function __construct(CategoryContract $category)
    {
        $this->category = $category;
    }


    public function getId(): ?int
    {
        return $this->category->getId();
    }

    public function getName(): ?string
    {
        return $this->category->getName();
    }

    public function getSortorder(): ?string
    {
        return $this->category->getSortorder();
    }

    public function getColor(): ?string
    {
        return $this->category->getColor();
    }

    public function getUrl(): ?string
    {
        return $this->category->getUrl();
    }

    public function getChildren(): ?Collection
    {
        return $this->category->getChildren();
    }

    public function getImage(): ?ImageContract
    {
        return $this->category->getImage();
    }

    public function getDescription(): ?string
    {
        return $this->category->getDescription();
    }

    public function getLanguage(): ?string
    {
        return $this->category->getLanguage();
    }

    public function getContentTeasers($page, $perPage, $orderBy, $order, $offset, $includeChildren, $notInCategory): Collection
    {
        return $this->category->getContentTeasers($page, $perPage, $orderBy, $order, $offset, $includeChildren, $notInCategory);
    }

    public function getContentTeasersCount($includeChildren): ?int
    {
        return $this->category->getContentTeasersCount($includeChildren);
    }

    public function getCount(): ?int
    {
        return $this->category->getCount();
    }

    public function getTeaser(string $type): ?TeaserContract
    {
        return $this->category->getTeaser($type);
    }

    public function getTeasers(): ?Collection
    {
        return $this->category->getTeasers();
    }

    public function getBody(): ?string
    {
        return $this->category->getBody();
    }

    public function getParent(): ?CategoryContract
    {
        return $this->category->getParent();
    }

    public function getAncestor(): ?CategoryContract
    {
        return $this->category->getAncestor();
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->category->getCanonicalUrl();
    }

    public function getContents(int $page = 1): ?Collection
    {
        return $this->category->getContents($page);
    }

    public function getTranslations(): ?Collection
    {
        return $this->category->getTranslations();
    }

    public function getContenthubId(): ?string
    {
        return $this->category->getContenthubId();
    }
}
