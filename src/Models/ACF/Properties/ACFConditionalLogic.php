<?php

namespace Bonnier\Willow\Base\Models\ACF\Properties;

use Illuminate\Contracts\Support\Arrayable;

class ACFConditionalLogic implements Arrayable
{
    public const OPERATOR_EQUALS = '==';
    public const OPERATOR_NOT_EQUALS = '!=';
    public const OPERATOR_NOT_EMPTY = '!=empty';

    /** @var array */
    private $rules = [];

    public function __construct(string $field = null, string $operator = null, string $value = null)
    {
        if ($field && $operator) {
            $this->add($field, $operator, $value);
        }
    }

    public function add(string $field, string $operator, string $value = null): ACFConditionalLogic
    {
        if (!in_array($operator, [self::OPERATOR_EQUALS, self::OPERATOR_NOT_EQUALS, self::OPERATOR_NOT_EMPTY])) {
            throw new \InvalidArgumentException(sprintf('\'%s\' is not a valid operator', $operator));
        }
        $rule = [
            'field' => $field,
            'operator' => $operator
        ];
        if ($value) {
            $rule['value'] = $value;
        }
        array_push($this->rules, [$rule]);

        return $this;
    }

    public function toArray()
    {
        return empty($this->rules) ? 0 : $this->rules;
    }
}
