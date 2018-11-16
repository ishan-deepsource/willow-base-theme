<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Pages;

use Bonnier\Willow\Base\Models\Contracts\Root\TranslationContract;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Traits\UrlTrait;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

class PageTranslationAdapter implements TranslationContract
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
        $permalink = WpModelRepository::instance()->getPermalink($this->getId());
        $locale = LanguageProvider::getPostLanguage($this->getId());
        return $this->getFullUrl($permalink, $locale) ?: null;
    }
}
