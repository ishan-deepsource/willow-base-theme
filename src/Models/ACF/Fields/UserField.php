<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class UserField extends ACFField
{
    public const TYPE = 'user';

    /** @var string  */
    private $role = '';
    /** @var bool  */
    private $allowNull = false;
    /** @var bool  */
    private $multiple = false;
    /** @var string  */
    private $returnFormat = self::RETURN_ARRAY;

    /**
     * @param string $role
     * @return UserField
     */
    public function setRole(string $role): UserField
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @param bool $allowNull
     * @return UserField
     */
    public function setAllowNull(bool $allowNull): UserField
    {
        $this->allowNull = $allowNull;
        return $this;
    }

    /**
     * @param bool $multiple
     * @return UserField
     */
    public function setMultiple(bool $multiple): UserField
    {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * @param string $returnFormat
     * @return UserField
     */
    public function setReturnFormat(string $returnFormat): UserField
    {
        if (!in_array($returnFormat, [self::RETURN_ID, self::RETURN_OBJECT, self::RETURN_ARRAY, self::RETURN_VALUE])) {
            throw new \InvalidArgumentException(sprintf('\'%s\' is not a valid return format', $returnFormat));
        }
        $this->returnFormat = $returnFormat;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'role' => $this->role,
            'allow_null' => $this->allowNull ? 1 : 0,
            'multiple' => $this->multiple ? 1 : 0,
            'return_format' => $this->returnFormat,
        ]);
    }
}
