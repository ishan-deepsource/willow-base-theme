<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Terms\Categories;

use Bonnier\Willow\Base\Adapters\Wp\Root\AbstractTeaserAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Categories\Partials\CategoryImageAdapter;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\MuPlugins\LanguageProvider;

class CategoryTeaserAdapter extends AbstractTeaserAdapter
{
    protected $meta;

    public function __construct($meta, $type)
    {
        $this->meta = $meta;
        parent::__construct($type);
    }

    public function getTitle(): string
    {
        return $this->meta->meta_title->{LanguageProvider::getCurrentLanguage()} ?? '';
    }

    public function getImage(): ?ImageContract
    {
        return new Image(new CategoryImageAdapter($this->meta));
    }

    public function getDescription(): string
    {
        return $this->meta->meta_description->{LanguageProvider::getCurrentLanguage()} ?? '';
    }
}
