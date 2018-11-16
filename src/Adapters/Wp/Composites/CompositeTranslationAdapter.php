<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites;

use Bonnier\Willow\Base\Models\Contracts\Root\TranslationContract;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Traits\UrlTrait;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

class CompositeTranslationAdapter implements TranslationContract
{
    use UrlTrait;

    protected $composite;

    public function __construct(\WP_Post $composite)
    {
        $this->composite = $composite;
    }

    public function getId(): ?int
    {
        return data_get($this->composite, 'ID') ?: null;
    }

    public function getTitle(): ?string
    {
        return data_get($this->composite, 'post_title') ?: null;
    }

    public function getLink(): ?string
    {
        $permalink = WpModelRepository::instance()->getPermalink($this->getId());
        $locale = LanguageProvider::getPostLanguage($this->getId());
        return $this->getFullUrl($permalink, $locale) ?: null;
    }
}
