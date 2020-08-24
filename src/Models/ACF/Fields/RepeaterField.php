<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;

class RepeaterField extends ACFField
{
    public const TYPE = 'repeater';

    private $collapsed = '';
    private $min = 0;
    private $max = 0;
    private $layout = 'table';
    private $buttonLabel = '';
    private $subFields = [];

    /**
     * @param string $collapsed
     * @return RepeaterField
     */
    public function setCollapsed(string $collapsed): RepeaterField
    {
        $this->collapsed = $collapsed;
        return $this;
    }

    /**
     * @param int $min
     * @return RepeaterField
     */
    public function setMin(int $min): RepeaterField
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @param int $max
     * @return RepeaterField
     */
    public function setMax(int $max): RepeaterField
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @param string $layout
     * @return RepeaterField
     */
    public function setLayout(string $layout): RepeaterField
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * @param string $buttonLabel
     * @return RepeaterField
     */
    public function setButtonLabel(string $buttonLabel): RepeaterField
    {
        $this->buttonLabel = $buttonLabel;
        return $this;
    }

    /**
     * @param array $subFields
     * @return RepeaterField
     */
    public function setSubFields(array $subFields): RepeaterField
    {
        $this->subFields = $subFields;
        return $this;
    }

    public function addSubField(ACFField $field): RepeaterField
    {
        array_push($this->subFields, $field);
        return $this;
    }

    /**
     * @return array
     */
    public function removeVideoUrlFromSubFields(): array
    {
        $subFields = array_filter($this->subFields, function (ACFField $field) {
            return $field->name !== CompositeFieldGroup::VIDEO_URL_FIELD_NAME;
        });
        return $subFields;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'collapsed' => $this->collapsed,
            'min' => $this->min,
            'max' => $this->max,
            'layout' => $this->layout,
            'button_label' => $this->buttonLabel,
            'sub_fields' => array_map(function (ACFField $field) {
                return $field->toArray();
            }, $this->subFields),
        ]);
    }
}
