<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class SelectField extends ACFField
{
    public const TYPE = 'select';

    private $choices = [];
    private $defaultValue = [];
    private $allowNull = false;
    private $multiple = false;
    private $ui = false;
    private $returnFormat = self::RETURN_VALUE;
    private $ajax = false;
    private $placeholder = '';

    /**
     * @param array $choices
     * @return SelectField
     */
    public function setChoices(array $choices): SelectField
    {
        $this->choices = $choices;
        return $this;
    }

    public function addChoice(string $key, string $value = null): SelectField
    {
        $this->choices[$key] = $value ?: $key;
        return $this;
    }

    /**
     * @param array $defaultValue
     * @return SelectField
     */
    public function setDefaultValue(array $defaultValue): SelectField
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    public function addDefaultValue($value): SelectField
    {
        array_push($this->defaultValue, $value);
        return $this;
    }

    /**
     * @param bool $allowNull
     * @return SelectField
     */
    public function setAllowNull(bool $allowNull): SelectField
    {
        $this->allowNull = $allowNull;
        return $this;
    }

    /**
     * @param bool $multiple
     * @return SelectField
     */
    public function setMultiple(bool $multiple): SelectField
    {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * @param bool $ui
     * @return SelectField
     */
    public function setUi(bool $ui): SelectField
    {
        $this->ui = $ui;
        return $this;
    }

    /**
     * @param string $returnFormat
     * @return SelectField
     */
    public function setReturnFormat(string $returnFormat): SelectField
    {
        if (!in_array($returnFormat, [self::RETURN_ID, self::RETURN_OBJECT, self::RETURN_ARRAY, self::RETURN_VALUE])) {
            throw new \InvalidArgumentException(sprintf('\'%s\' is not a valid return format', $returnFormat));
        }
        $this->returnFormat = $returnFormat;
        return $this;
    }

    /**
     * @param bool $ajax
     * @return SelectField
     */
    public function setAjax(bool $ajax): SelectField
    {
        $this->ajax = $ajax;
        return $this;
    }

    /**
     * @param string $placeholder
     * @return SelectField
     */
    public function setPlaceholder(string $placeholder): SelectField
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'choices' => $this->choices,
            'default_value' => $this->defaultValue,
            'allow_null' => $this->allowNull ? 1 : 0,
            'multiple' => $this->multiple ? 1 : 0,
            'ui' => $this->ui ? 1 : 0,
            'return_format' => $this->returnFormat,
            'ajax' => $this->ajax ? 1 : 0,
            'placeholder' => $this->placeholder,
        ]);
    }
}
