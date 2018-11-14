<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Terms\Categories;

use Bonnier\Willow\Base\Models\Contracts\Terms\CategoryTranslationContract;
use Bonnier\Willow\Base\Traits\UrlTrait;

class CategoryTranslationAdapter implements CategoryTranslationContract
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
        return data_get($this->category, 'name') ?: null;
    }

    public function getLink(): ?string
    {
        return $this->getFullUrl(get_term_link($this->getId())) ?: null;
    }
}
