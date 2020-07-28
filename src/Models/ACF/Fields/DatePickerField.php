<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class DatePickerField extends ACFField
{
    public const TYPE = 'date_picker';

    /** @var string */
    private $displayFormat = 'd/m/Y';
    /** @var string */
    private $returnFormat = 'd/m/Y';
    /** @var int */
    private $firstDay = 0;

    /**
     * @param string $displayFormat
     * @return DatePickerField
     */
    public function setDisplayFormat(string $displayFormat): DatePickerField
    {
        $this->displayFormat = $displayFormat;
        return $this;
    }

    /**
     * @param string $returnFormat
     * @return DatePickerField
     */
    public function setReturnFormat(string $returnFormat): DatePickerField
    {
        $this->returnFormat = $returnFormat;
        return $this;
    }

    /**
     * @param int $firstDay
     * @return DatePickerField
     */
    public function setFirstDay(int $firstDay): DatePickerField
    {
        $this->firstDay = $firstDay;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'display_format' => $this->displayFormat,
            'return_format' => $this->returnFormat,
            'first_day' => $this->firstDay,
        ]);
    }
}
