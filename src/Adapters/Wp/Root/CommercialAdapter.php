<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Root\CommercialContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

class CommercialAdapter implements CommercialContract
{
    protected $acFields;

    public function __construct($acFields)
    {
        $this->acFields = $acFields;
    }


    public function getType(): ?string
    {
        return array_get($this->acFields, 'commercial_type') ?: null;
    }

    public function getLabel(): ?string
    {
        if ($commercialType = $this->getType()) {
            return LanguageProvider::translate($commercialType);
        }
        return null;
    }

    public function getLogo(): ?ImageContract
    {
        if ($logo = array_get($this->acFields, 'commercial_logo')) {
            $postMeta = get_post_meta(data_get($logo, 'ID'));
            $attachmentMeta = wp_get_attachment_metadata(data_get($logo, 'ID'));
            return new Image(new ImageAdapter($logo, $postMeta, $attachmentMeta));
        }
        return null;
    }
}
