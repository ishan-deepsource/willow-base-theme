<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class TrueFalseField extends ACFField
{
    public const TYPE = 'true_false';

    /** @var string  */
    private $message = '';
    /** @var bool  */
    private $defaultValue = false;
    /** @var bool  */
    private $ui = false;
    /** @var string  */
    private $uiOnText = '';
    /** @var string  */
    private $uiOffText = '';

    /**
     * @param string $message
     * @return TrueFalseField
     */
    public function setMessage(string $message): TrueFalseField
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param bool $defaultValue
     * @return TrueFalseField
     */
    public function setDefaultValue(bool $defaultValue): TrueFalseField
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @param bool $ui
     * @return TrueFalseField
     */
    public function setUi(bool $ui): TrueFalseField
    {
        $this->ui = $ui;
        return $this;
    }

    /**
     * @param string $uiOnText
     * @return TrueFalseField
     */
    public function setUiOnText(string $uiOnText): TrueFalseField
    {
        $this->uiOnText = $uiOnText;
        return $this;
    }

    /**
     * @param string $uiOffText
     * @return TrueFalseField
     */
    public function setUiOffText(string $uiOffText): TrueFalseField
    {
        $this->uiOffText = $uiOffText;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'message' => $this->message,
            'default_value' => $this->defaultValue ? 1 : 0,
            'ui' => $this->ui ? 1 : 0,
            'ui_on_text' => $this->uiOnText,
            'ui_off_text' => $this->uiOffText,
        ]);
    }
}
