<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class TextAreaField extends ACFField
{
    public const TYPE = 'textarea';

    /** @var string  */
    private $defaultValue = '';
    /** @var string  */
    private $placeholder = '';
    /** @var string  */
    private $maxlength = '';
    /** @var string  */
    private $rows = '';
    /** @var string  */
    private $newLines = '';
    /** @var bool  */
    private $readonly = false;
    /** @var bool  */
    private $disabled = false;

    /**
     * @param string $defaultValue
     * @return TextAreaField
     */
    public function setDefaultValue(string $defaultValue): TextAreaField
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @param string $placeholder
     * @return TextAreaField
     */
    public function setPlaceholder(string $placeholder): TextAreaField
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * @param string $maxlength
     * @return TextAreaField
     */
    public function setMaxlength(string $maxlength): TextAreaField
    {
        $this->maxlength = $maxlength;
        return $this;
    }

    /**
     * @param string $rows
     * @return TextAreaField
     */
    public function setRows(string $rows): TextAreaField
    {
        $this->rows = $rows;
        return $this;
    }

    /**
     * @param string $newLines
     * @return TextAreaField
     */
    public function setNewLines(string $newLines): TextAreaField
    {
        $this->newLines = $newLines;
        return $this;
    }

    /**
     * @param bool $readonly
     * @return TextAreaField
     */
    public function setReadonly(bool $readonly): TextAreaField
    {
        $this->readonly = $readonly;
        return $this;
    }

    /**
     * @param bool $disabled
     * @return TextAreaField
     */
    public function setDisabled(bool $disabled): TextAreaField
    {
        $this->disabled = $disabled;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'default_value' => $this->defaultValue,
            'placeholder' => $this->placeholder,
            'maxlength' => $this->maxlength,
            'rows' => $this->rows,
            'new_lines' => $this->newLines,
            'readonly' => $this->readonly ? 1 : 0,
            'disabled' => $this->disabled ? 1 : 0,
        ]);
    }
}
