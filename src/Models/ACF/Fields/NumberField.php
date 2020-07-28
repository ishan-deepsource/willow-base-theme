<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class NumberField extends ACFField
{
    public const TYPE = 'number';

    /** @var string  */
    private $defaultValue = '';
    /** @var string  */
    private $placeholder = '';
    /** @var string  */
    private $prepend = '';
    /** @var string  */
    private $append = '';
    /** @var int  */
    private $min = 0;
    /** @var int  */
    private $max = 0;
    /** @var string  */
    private $step = '';

    /**
     * @param string $defaultValue
     * @return NumberField
     */
    public function setDefaultValue(string $defaultValue): NumberField
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @param string $placeholder
     * @return NumberField
     */
    public function setPlaceholder(string $placeholder): NumberField
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * @param string $prepend
     * @return NumberField
     */
    public function setPrepend(string $prepend): NumberField
    {
        $this->prepend = $prepend;
        return $this;
    }

    /**
     * @param string $append
     * @return NumberField
     */
    public function setAppend(string $append): NumberField
    {
        $this->append = $append;
        return $this;
    }

    /**
     * @param int $min
     * @return NumberField
     */
    public function setMin(int $min): NumberField
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @param int $max
     * @return NumberField
     */
    public function setMax(int $max): NumberField
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @param string $step
     * @return NumberField
     */
    public function setStep(string $step): NumberField
    {
        $this->step = $step;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'default_value' => $this->defaultValue,
            'placeholder' => $this->placeholder,
            'prepend' => $this->prepend,
            'append' => $this->append,
            'min' => $this->min,
            'max' => $this->max,
            'step' => $this->step,
        ]);
    }
}
