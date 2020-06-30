<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class RadioField extends ACFField
{
    public const TYPE = 'radio';

    /** @var array */
    private $choices = [];
    /** @var int */
    private $otherChoice = 0;
    /** @var int */
    private $saveOtherChoice = 0;
    /** @var string */
    private $defaultValue = '';
    /** @var string */
    private $layout;
    /** @var bool */
    private $allowNull;
    /** @var string */
    private $returnFormat = self::RETURN_VALUE;

    /**
     * @param array $choices
     * @return RadioField
     */
    public function setChoices(array $choices): RadioField
    {
        $this->choices = $choices;
        return $this;
    }

    public function setChoice(string $key, string $value): RadioField
    {
        $this->choices[$key] = $value;
        return $this;
    }

    public function removeChoice(string $key): RadioField
    {
        unset($this->choices[$key]);
        return $this;
    }

    /**
     * @param int $otherChoice
     * @return RadioField
     */
    public function setOtherChoice(int $otherChoice): RadioField
    {
        $this->otherChoice = $otherChoice;
        return $this;
    }

    /**
     * @param int $saveOtherChoice
     * @return RadioField
     */
    public function setSaveOtherChoice(int $saveOtherChoice): RadioField
    {
        $this->saveOtherChoice = $saveOtherChoice;
        return $this;
    }

    /**
     * @param string $defaultValue
     * @return RadioField
     */
    public function setDefaultValue(string $defaultValue): RadioField
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @param string $layout
     * @return RadioField
     */
    public function setLayout(string $layout): RadioField
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * @param bool $allowNull
     * @return RadioField
     */
    public function setAllowNull(bool $allowNull): RadioField
    {
        $this->allowNull = $allowNull;
        return $this;
    }

    /**
     * @param string $returnFormat
     * @return RadioField
     */
    public function setReturnFormat(string $returnFormat): RadioField
    {
        if (!in_array($returnFormat, [self::RETURN_ID, self::RETURN_OBJECT, self::RETURN_ARRAY, self::RETURN_VALUE])) {
            throw new \InvalidArgumentException(sprintf('\'%s\' is not a valid return format', $returnFormat));
        }
        $this->returnFormat = $returnFormat;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'choices' => $this->choices,
            'other_choice' => $this->otherChoice,
            'save_other_choice' => $this->saveOtherChoice,
            'default_value' => $this->defaultValue,
            'layout' => $this->layout,
            'allow_null' => $this->allowNull ? 1 : 0,
            'return_format' => $this->returnFormat,
        ]);
    }
}
