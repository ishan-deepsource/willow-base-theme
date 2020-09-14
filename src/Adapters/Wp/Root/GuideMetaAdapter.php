<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Models\ACF\Composite\GuideMetaFieldGroup;
use Bonnier\Willow\Base\Models\Contracts\Root\GuideMetaContract;


class GuideMetaAdapter implements GuideMetaContract {
    protected $acFields;

    public function __construct($acFields)
    {
        $this->acFields = $acFields;
    }

    public function getDifficulty(): ?int
    {
        $difficultyObj = array_get($this->acFields, GuideMetaFieldGroup::DIFFICULTY_FIELD_NAME) ?: null;
        return !empty($difficultyObj) ? (int) $difficultyObj->name : null;
    }

    public function getTimeRequired(): ?string
    {
        return array_get($this->acFields, GuideMetaFieldGroup::TIME_REQUIRED_FIELD_NAME);
    }

    public function getPrice(): ?string
    {
        return array_get($this->acFields, GuideMetaFieldGroup::PRICE_FIELD_NAME);
    }
}
