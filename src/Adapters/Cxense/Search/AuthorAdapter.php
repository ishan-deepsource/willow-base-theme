<?php

namespace Bonnier\Willow\Base\Adapters\Cxense\Search;

use Bonnier\WP\Cxense\Parsers\Document;
use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use DateTime;
use Illuminate\Support\Collection;

/**
 * Class AuthorAdapter
 * @package Bonnier\Willow\Base\Adapters\Cxense\Search
 */
class AuthorAdapter implements AuthorContract
{
    protected $name;

    public function __construct(Document $document)
    {
        $this->name = $document->getField('author');
    }

    public function getId(): ?int
    {
        return null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getBiography(): ?string
    {
        return null;
    }

    public function getAvatar(): ?ImageContract
    {
        return null;
    }

    public function getTitle(): ?string
    {
        return null;
    }

    public function getEducation(): ?string
    {
        return null;
    }

    public function getUrl(): ?string
    {
        return null;
    }

    public function getWebsite(): ?string
    {
        return null;
    }

    public function getEmail(): ?string
    {
        return null;
    }

    public function getContentTeasers($page, $perPage, $orderBy, $order, $offset): Collection
    {
        return collect();
    }

    public function getBirthday(): ?DateTime
    {
        return null;
    }

    public function isPublic(): bool
    {
        return false;
    }

    public function isAuthor(): bool
    {
        return false;
    }

    public function getCount(): int
    {
        return 0;
    }
}
