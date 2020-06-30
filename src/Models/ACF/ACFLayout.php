<?php

namespace Bonnier\Willow\Base\Models\ACF;

use Illuminate\Contracts\Support\Arrayable;

class ACFLayout implements Arrayable
{
    private $key;
    private $name;
    private $label;
    private $display = 'block';
    private $subFields = [];
    private $min = '';
    private $max = '';

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     * @return ACFLayout
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @param mixed $name
     * @return ACFLayout
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $label
     * @return ACFLayout
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param string $display
     * @return ACFLayout
     */
    public function setDisplay(string $display): ACFLayout
    {
        $this->display = $display;
        return $this;
    }

    /**
     * @param array|ACFField[] $subFields
     * @return ACFLayout
     */
    public function setSubFields(array $subFields): ACFLayout
    {
        $this->subFields = $subFields;
        return $this;
    }

    public function addSubField(ACFField $field): ACFLayout
    {
        array_push($this->subFields, $field);
        return $this;
    }

    public function addSubFields(array $fields): ACFLayout
    {
        $this->subFields = array_merge($this->subFields, $fields);
        return $this;
    }

    /**
     * @return array
     */
    public function getSubFields(): array
    {
        return $this->subFields;
    }

    public function mapSubFields(callable $callback): ACFLayout
    {
        $this->subFields = array_map($callback, $this->subFields);
        return $this;
    }

    /**
     * @param string $min
     * @return ACFLayout
     */
    public function setMin(string $min): ACFLayout
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @param string $max
     * @return ACFLayout
     */
    public function setMax(string $max): ACFLayout
    {
        $this->max = $max;
        return $this;
    }

    public function toArray()
    {
        return [
            'key' => $this->key,
            'name' => $this->name,
            'label' => $this->label,
            'display' => $this->display,
            'sub_fields' => array_map(function (ACFField $field) {
                return $field->toArray();
            }, $this->subFields),
            'min' => $this->min,
            'max' => $this->max,
        ];
    }
}
