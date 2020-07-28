<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class GroupField extends ACFField
{
    public const TYPE = 'group';

    private $layout = 'block';
    private $subFields = [];

    /**
     * @param string $layout
     * @return GroupField
     */
    public function setLayout(string $layout): GroupField
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * @param array $subFields
     * @return GroupField
     */
    public function setSubFields(array $subFields): GroupField
    {
        $this->subFields = $subFields;
        return $this;
    }

    public function addSubField(ACFField $field): GroupField
    {
        array_push($this->subFields, $field);
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'layout' => $this->layout,
            'sub_fields' => array_map(function (ACFField $field) {
                return $field->toArray();
            }, $this->subFields),
        ]);
    }
}
