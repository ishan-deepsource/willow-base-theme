<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Terms\Tags;

use Bonnier\Willow\Base\Models\Contracts\Root\TranslationContract;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Traits\UrlTrait;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

class TagTranslationAdapter implements TranslationContract
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
        if ($title = data_get($this->tag, 'name')) {
            return htmlspecialchars_decode($title);
        }
        return null;
    }

    public function getLink(): ?string
    {
        $permalink = WpModelRepository::instance()->getTagLink($this->getId());
        $locale = LanguageProvider::getTermLanguage($this->getId());
        return $this->getFullUrl($permalink, $locale) ?: null;
    }
}
