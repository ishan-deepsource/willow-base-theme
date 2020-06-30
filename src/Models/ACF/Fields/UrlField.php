<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class UrlField extends ACFField
{
    public const TYPE = 'url';

    private $defaultValue = '';
    private $placeholder = '';

    /**
     * @param string $defaultValue
     * @return UrlField
     */
    public function setDefaultValue(string $defaultValue): UrlField
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @param string $placeholder
     * @return UrlField
     */
    public function setPlaceholder(string $placeholder): UrlField
    {
        $this->placeholder = $placeholder;
        return $this;
    }



    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'default_value' => $this->defaultValue,
            'placeholder' => $this->placeholder,
        ]);
    }
}
