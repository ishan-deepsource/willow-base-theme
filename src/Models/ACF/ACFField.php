<?php

namespace Bonnier\Willow\Base\Models\ACF;

use Bonnier\Willow\Base\Models\ACF\Properties\ACFConditionalLogic;
use Bonnier\Willow\Base\Models\ACF\Properties\ACFWrapper;
use Illuminate\Contracts\Support\Arrayable;

abstract class ACFField implements Arrayable
{
    public const TYPE = 'PLEASE_IMPLEMENT';

    public const RETURN_ID = 'id';
    public const RETURN_OBJECT = 'object';
    public const RETURN_ARRAY = 'array';
    public const RETURN_VALUE = 'value';
    public const RETURN_DATE = 'Y-m-d';


    /** @var string */
    protected $key;
    /** @var string */
    protected $label = '';
    /** @var string */
    protected $name = '';
    /** @var string */
    protected $type = '';
    /** @var string */
    protected $instructions = '';
    /** @var boolean */
    protected $required = false;
    /** @var ACFConditionalLogic */
    protected $conditionalLogic;
    /** @var ACFWrapper */
    protected $wrapper;

    public function __construct(string $key)
    {
        $this->key = $key;
        if (static::TYPE === 'PLEASE_IMPLEMENT') {
            throw new \InvalidArgumentException('public const TYPE must be overwritten!');
        }
        $this->type = static::TYPE;
        $this->wrapper = new ACFWrapper();
        $this->conditionalLogic = new ACFConditionalLogic();
    }

    /**
     * @param string $key
     * @return ACFField
     */
    public function setKey(string $key): ACFField
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $label
     * @return ACFField
     */
    public function setLabel(string $label): ACFField
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param string $name
     * @return ACFField
     */
    public function setName(string $name): ACFField
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $type
     * @return ACFField
     */
    public function setType(string $type): ACFField
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $instructions
     * @return ACFField
     */
    public function setInstructions(string $instructions): ACFField
    {
        $this->instructions = $instructions;
        return $this;
    }

    /**
     * @param bool $required
     * @return ACFField
     */
    public function setRequired(bool $required): ACFField
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @param ACFConditionalLogic $conditionalLogic
     * @return ACFField
     */
    public function setConditionalLogic(ACFConditionalLogic $conditionalLogic): ACFField
    {
        $this->conditionalLogic = $conditionalLogic;
        return $this;
    }

    /**
     * @param ACFWrapper $wrapper
     * @return ACFField
     */
    public function setWrapper(ACFWrapper $wrapper): ACFField
    {
        $this->wrapper = $wrapper;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'name' => $this->name,
            'type' => $this->type,
            'instructions' => $this->instructions,
            'required' => $this->required ? 1 : 0,
            'conditional_logic' => $this->conditionalLogic->toArray(),
            'wrapper' => $this->wrapper->toArray(),
        ];
    }
}
