<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Root\CommercialContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\MuPlugins\LanguageProvider;

class CommercialAdapter implements CommercialContract
{
    protected $acFields;

    public function __construct($acFields)
    {
        $this->acFields = $acFields;
    }


    public function getType(): ?string
    {
        return $this->acFields['commercial_type'] ?? null;
    }

    public function getLabel(): ?string
    {
        $commercialType = $this->getType();
        $translation = LanguageProvider::translate($commercialType);
        if ($commercialType && ! empty($translation)) {
            return $translation;
        }
        return null;
    }

    public function getLogo(): ?ImageContract
    {
        if (!empty($this->acFields['commercial_logo'] ?? null)) {
            if ($logo = get_post($this->acFields['commercial_logo'])) {
                return new Image(new ImageAdapter($logo));
            }
        }
        return null;
    }
}
