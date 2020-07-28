<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class TextField extends ACFField
{
    public const TYPE = 'text';

    /** @var string  */
    private $defaultValue = '';
    /** @var string  */
    private $placeholder = '';
    /** @var string  */
    private $prepend = '';
    /** @var string  */
    private $append = '';
    /** @var string  */
    private $maxlength = '';

    /**
     * @param string $defaultValue
     * @return TextField
     */
    public function setDefaultValue(string $defaultValue): TextField
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @param string $placeholder
     * @return TextField
     */
    public function setPlaceholder(string $placeholder): TextField
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * @param string $prepend
     * @return TextField
     */
    public function setPrepend(string $prepend): TextField
    {
        $this->prepend = $prepend;
        return $this;
    }

    /**
     * @param string $append
     * @return TextField
     */
    public function setAppend(string $append): TextField
    {
        $this->append = $append;
        return $this;
    }

    /**
     * @param string $maxlength
     * @return TextField
     */
    public function setMaxlength(string $maxlength): TextField
    {
        $this->maxlength = $maxlength;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'default_value' => $this->defaultValue,
            'placeholder' => $this->placeholder,
            'prepend' => $this->prepend,
            'append' => $this->append,
            'maxlength' => $this->maxlength,
        ]);
    }
}
