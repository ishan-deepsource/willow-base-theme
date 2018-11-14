<?php

namespace Bonnier\Willow\Base\Models\Base\Terms;

use Bonnier\Willow\Base\Models\Contracts\Terms\CategoryTranslationContract;

class CategoryTranslation implements CategoryTranslationContract
{
    protected $categoryTranslation;

    public function __construct(CategoryTranslationContract $categoryTranslation)
    {
        $this->categoryTranslation = $categoryTranslation;
    }

    public function getId(): ?int
    {
        return $this->categoryTranslation->getId();
    }

    public function getTitle(): ?string
    {
        return $this->categoryTranslation->getTitle();
    }

    public function getLink(): ?string
    {
        return $this->categoryTranslation->getLink();
    }
}
