<?php

namespace Bonnier\Willow\Base\Models\Base\Terms;

use Bonnier\Willow\Base\Models\Contracts\Terms\TagTranslationContract;

class TagTranslation implements TagTranslationContract
{
    protected $tagTranslation;

    public function __construct(TagTranslationContract $tagTranslation)
    {
        $this->tagTranslation = $tagTranslation;
    }

    public function getId(): ?int
    {
        return $this->tagTranslation->getId();
    }

    public function getTitle(): ?string
    {
        return $this->tagTranslation->getTitle();
    }

    public function getLink(): ?string
    {
        return $this->tagTranslation->getLink();
    }
}
