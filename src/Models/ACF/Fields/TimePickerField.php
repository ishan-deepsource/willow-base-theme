<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class TimePickerField extends ACFField
{
    public const TYPE = 'time_picker';

    /** @var string */
    private $displayFormat = 'H:i:s';
    /** @var string */
    private $returnFormat = 'H:i:s';
    
    /**
     * @param string $displayFormat
     * @return TimePickerField
     */
    public function setDisplayFormat(string $displayFormat): TimePickerField
    {
        $this->displayFormat = $displayFormat;
        return $this;
    }

    /**
     * @param string $returnFormat
     * @return TimePickerField
     */
    public function setReturnFormat(string $returnFormat): TimePickerField
    {
        $this->returnFormat = $returnFormat;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'display_format' => $this->displayFormat,
            'return_format' => $this->returnFormat,
        ]);
    }
}