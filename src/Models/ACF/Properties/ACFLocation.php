<?php

namespace Bonnier\Willow\Base\Models\ACF\Properties;

class ACFLocation
{
    public const OPERATOR_EQUALS = '==';
    public const OPERATOR_NOT_EQUALS = '!==';

    private $rules = [];

    public function __construct(string $param = null, string $operator = null, string $value = null)
    {
        if ($param && $operator && $value) {
            $this->addLocation($param, $operator, $value);
        }
    }

    public function addLocation(string $param, string $operator, string $value): ACFLocation
    {
        if (!in_array($operator, [self::OPERATOR_EQUALS, self::OPERATOR_NOT_EQUALS])) {
            throw new \InvalidArgumentException(sprintf('\'%s\' is not a valid operator', $operator));
        }
        array_push($this->rules, [[
            'param' => $param,
            'operator' => $operator,
            'value' => $value
        ]]);
        return $this;
    }

    /**
     * @return array[]|int
     */
    public function toArray()
    {
        return empty($this->rules) ? 0 : $this->rules;
    }
}
