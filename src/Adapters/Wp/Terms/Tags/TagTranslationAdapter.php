<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Terms\Tags;

use Bonnier\Willow\Base\Factories\DataFactory;
use Bonnier\Willow\Base\Models\Contracts\Terms\TagTranslationContract;
use Bonnier\Willow\Base\Traits\UrlTrait;

class TagTranslationAdapter implements TagTranslationContract
{
    use UrlTrait;

    protected $tag;

    public function __construct(\WP_Term $tag)
    {
        $this->tag = $tag;
    }

    public function getId(): ?int
    {
        return data_get($this->tag, 'term_id') ?: null;
    }

    public function getTitle(): ?string
    {
        return data_get($this->tag, 'name') ?: null;
    }

    public function getLink(): ?string
    {
        return $this->getFullUrl(DataFactory::instance()->getTagLink($this->getId())) ?: null;
    }
}
