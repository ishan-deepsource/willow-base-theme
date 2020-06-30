<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class CheckboxField extends ACFField
{
    public const TYPE = 'checkbox';

    private $choices = [];
    private $message = '';
    private $defaultValue = [];
    private $ui = false;
    private $uiOnText = '';
    private $uiOffText = '';

    /**
     * @param array $choices
     * @return CheckboxField
     */
    public function setChoices(array $choices): CheckboxField
    {
        $this->choices = $choices;
        return $this;
    }

    public function addChoice(string $key, string $value = null): CheckboxField
    {
        if (is_null($value)) {
            $this->choices[$key] = $key;
        } else {
            $this->choices[$key] = $value;
        }

        return $this;
    }

    /**
     * @param string $message
     * @return CheckboxField
     */
    public function setMessage(string $message): CheckboxField
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param array $defaultValue
     * @return CheckboxField
     */
    public function setDefaultValue(array $defaultValue): CheckboxField
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @param bool $ui
     * @return CheckboxField
     */
    public function setUi(bool $ui): CheckboxField
    {
        $this->ui = $ui;
        return $this;
    }

    /**
     * @param string $uiOnText
     * @return CheckboxField
     */
    public function setUiOnText(string $uiOnText): CheckboxField
    {
        $this->uiOnText = $uiOnText;
        return $this;
    }

    /**
     * @param string $uiOffText
     * @return CheckboxField
     */
    public function setUiOffText(string $uiOffText): CheckboxField
    {
        $this->uiOffText = $uiOffText;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'choices' => $this->choices,
            'message' => $this->message,
            'default_value' => $this->defaultValue,
            'ui' => $this->ui ? 1 : 0,
            'ui_on_text' => $this->uiOnText,
            'ui_off_text' => $this->uiOffText,
        ]);
    }
}
