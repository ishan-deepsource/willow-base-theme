<?php

namespace Bonnier\Willow\Base\Models\Base\Pages;

use Bonnier\Willow\Base\Models\Contracts\Pages\PageTranslationContract;

class PageTranslation implements PageTranslationContract
{
    protected $pageTranslation;

    public function __construct(PageTranslationContract $pageTranslation)
    {
        $this->pageTranslation = $pageTranslation;
    }

    public function getId(): ?int
    {
        return $this->pageTranslation->getId();
    }

    public function getTitle(): ?string
    {
        return $this->pageTranslation->getTitle();
    }

    public function getLink(): ?string
    {
        return $this->pageTranslation->getLink();
    }
}
