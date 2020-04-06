<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Terms\Categories;

use Bonnier\Willow\Base\Models\Contracts\Root\TranslationContract;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Traits\UrlTrait;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

class CategoryTranslationAdapter implements TranslationContract
{
    use UrlTrait;

    protected $category;

    public function __construct(\WP_Term $category)
    {
        $this->category = $category;
    }

    public function getId(): ?int
    {
        return data_get($this->category, 'term_id') ?: null;
    }

    public function getTitle(): ?string
    {
        if ($title = data_get($this->category, 'name')) {
            return htmlspecialchars_decode($title);
        }

        return null;
    }

    public function getLink(): ?string
    {
        $permalink = WpModelRepository::instance()->getTermlink($this->getId());
        $locale = LanguageProvider::getTermLanguage($this->getId());
        return $this->getFullUrl($permalink, $locale) ?: null;
    }
}
