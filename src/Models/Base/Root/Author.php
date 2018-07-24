<?php

namespace Bonnier\Willow\Base\Models\Base\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

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

    public function getTitle(): ?string
    {
        return $this->author->getTitle();
    }
}
