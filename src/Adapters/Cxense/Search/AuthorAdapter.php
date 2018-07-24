<?php

namespace Bonnier\Willow\Base\Adapters\Cxense\Search;

use Bonnier\WP\Cxense\Parsers\Document;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

/**
 * Class DocumentAdapter
 *
 * @package \\${NAMESPACE}
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

    public function getUrl(): ?string
    {
        return null;
    }
}
