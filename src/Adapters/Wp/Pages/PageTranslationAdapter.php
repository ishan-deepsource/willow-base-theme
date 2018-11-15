<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Pages;

use Bonnier\Willow\Base\Factories\DataFactory;
use Bonnier\Willow\Base\Models\Contracts\Pages\PageTranslationContract;
use Bonnier\Willow\Base\Traits\UrlTrait;

class PageTranslationAdapter implements PageTranslationContract
{
    use UrlTrait;

    protected $page;

    public function __construct(\WP_Post $page)
    {
        $this->page = $page;
    }

    public function getId(): ?int
    {
        return data_get($this->page, 'ID') ?: null;
    }

    public function getTitle(): ?string
    {
        return data_get($this->page, 'post_title') ?: null;
    }

    public function getLink(): ?string
    {
        return $this->getFullUrl(DataFactory::instance()->getPermalink($this->getId())) ?: null;
    }
}
