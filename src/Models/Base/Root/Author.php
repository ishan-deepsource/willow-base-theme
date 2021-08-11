<?php

namespace Bonnier\Willow\Base\Models\Base\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use DateTime;
use Illuminate\Support\Collection;

class Author implements AuthorContract
{
    protected $author;

    public function __construct(AuthorContract $author)
    {
        $this->author = $author;
    }

    public function getId(): ?int
    {
        return $this->author->getId();
    }

    public function getName(): ?string
    {
        return $this->author->getName();
    }

    public function getBiography(): ?string
    {
        return $this->author->getBiography();
    }

    public function getAvatar(): ?ImageContract
    {
        return $this->author->getAvatar();
    }

    public function getUrl(): ?string
    {
        return $this->author->getUrl();
    }

    public function getWebsite(): ?string
    {
        return $this->author->getWebsite();
    }

    public function getEmail(): ?string
    {
        return $this->author->getEmail();
    }

    public function getTitle(): ?string
    {
        return $this->author->getTitle();
    }

    public function getEducation(): ?string
    {
        return $this->author->getEducation();
    }

    public function getContentTeasers($page, $perPage, $orderBy, $order, $offset): Collection
    {
        return $this->author->getContentTeasers($page, $perPage, $orderBy, $order, $offset);
    }

    public function getBirthday(): ?DateTime
    {
        return $this->author->getBirthday();
    }

    public function isPublic(): bool
    {
        return $this->author->isPublic();
    }

    public function isAuthor(): bool
    {
        return $this->author->isAuthor();
    }

    public function getCount(): int
    {
        return $this->author->getCount();
    }
}
