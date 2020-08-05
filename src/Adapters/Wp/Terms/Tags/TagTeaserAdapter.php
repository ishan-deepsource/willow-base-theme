<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Terms\Tags;

use Bonnier\Willow\Base\Adapters\Wp\Root\AbstractTeaserAdapter;
use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Composite\TeaserFieldGroup;
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
        if ($title = data_get($this->meta, 'meta_title.' . LanguageProvider::getCurrentLanguage())) {
            return htmlspecialchars_decode($title);
        }
        return null;
    }

    public function getImage(): ?ImageContract
    {
        return null;
    }

    public function getVideoUrl(): ?string
    {
        return array_get($this->acfArray, TeaserFieldGroup::VIDEO_URL_FIELD_NAME) ?: null;
    }

    public function getDescription(): ?string
    {
        return data_get($this->meta, 'meta_description.' . LanguageProvider::getCurrentLanguage()) ?: null;
    }
}
