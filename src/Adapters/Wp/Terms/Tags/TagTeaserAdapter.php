<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Terms\Tags;

use Bonnier\Willow\Base\Adapters\Wp\Root\AbstractTeaserAdapter;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

class TagTeaserAdapter extends AbstractTeaserAdapter
{
    protected $meta;

    public function __construct($meta, $type)
    {
        $this->meta = $meta;
        parent::__construct($type);
    }

    public function getTitle(): ?string
    {
        return data_get($this->meta, 'meta_title.' . LanguageProvider::getCurrentLanguage()) ?: null;
    }

    public function getImage(): ?ImageContract
    {
        return null;
    }

    public function getDescription(): ?string
    {
        return data_get($this->meta, 'meta_description.' . LanguageProvider::getCurrentLanguage()) ?: null;
    }
}
