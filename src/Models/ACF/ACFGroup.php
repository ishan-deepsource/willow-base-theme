<?php

namespace Bonnier\Willow\Base\Models\ACF;

use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;
use Bonnier\Willow\Base\Models\ACF\Properties\ACFLocation;
use Illuminate\Contracts\Support\Arrayable;

class ACFGroup implements Arrayable
{
    public const POSITION_NORMAL = 'normal';
    public const POSITION_AFTER_TITLE = 'acf_after_title';
    public const POSITION_SIDE = 'side';

    public const STYLE_DEFAULT = 'default';
    public const STYLE_SEAMLESS = 'seamless';

    public const LABEL_PLACEMENT_TOP = 'top';

    public const INSTRUCTION_PLACEMENT_LABEL = 'label';

    /** @var string */
    private $key;
    /** @var string */
    private $title;
    /** @var array|ACFField[] */
    private $fields = [];
    /** @var ACFLocation */
    private $location;
    /** @var int */
    private $menuOrder = 0;
    /** @var string */
    private $position = self::POSITION_NORMAL;
    /** @var string */
    private $style = self::STYLE_DEFAULT;
    /** @var string */
    private $labelPlacement = self::LABEL_PLACEMENT_TOP;
    /** @var string */
    private $instructionPlacement = self::INSTRUCTION_PLACEMENT_LABEL;
    /** @var array */
    private $hideOnScreen = [];
    /** @var boolean */
    private $active = true;
    /** @var string */
    private $description = '';

    public function __construct(string $key)
    {
        $this->key = $key;
        $this->location = new ACFLocation();
    }

    /**
     * @param string $key
     * @return ACFGroup
     */
    public function setKey(string $key): ACFGroup
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
     * @param string $title
     * @return ACFGroup
     */
    public function setTitle(string $title): ACFGroup
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array|ACFField[] $fields
     * @return ACFGroup
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    public function addField(ACFField $field): ACFGroup
    {
        array_push($this->fields, $field);
        return $this;
    }

    public function removeField(string $fieldKey): ACFGroup
    {
        $this->fields = array_filter($this->fields, function (ACFField $field) use ($fieldKey) {
            return $field->getKey() !== $fieldKey;
        });
        return $this;
    }

    /**
     * @param ACFLocation $location
     * @return ACFGroup
     */
    public function setLocation(ACFLocation $location): ACFGroup
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @param int $menuOrder
     * @return ACFGroup
     */
    public function setMenuOrder(int $menuOrder): ACFGroup
    {
        $this->menuOrder = $menuOrder;
        return $this;
    }

    /**
     * @param string $position
     * @return ACFGroup
     */
    public function setPosition(string $position): ACFGroup
    {
        if (!in_array($position, [self::POSITION_AFTER_TITLE, self::POSITION_SIDE, self::POSITION_NORMAL])) {
            throw new \InvalidArgumentException(sprintf('\'%s\' is not a valid position', $position));
        }
        $this->position = $position;
        return $this;
    }

    /**
     * @param string $style
     * @return ACFGroup
     */
    public function setStyle(string $style): ACFGroup
    {
        if (!in_array($style, [self::STYLE_SEAMLESS, self::STYLE_DEFAULT])) {
            throw new \InvalidArgumentException(sprintf('\'%s\' is not a valid style', $style));
        }

        $this->style = $style;
        return $this;
    }

    /**
     * @param string $labelPlacement
     * @return ACFGroup
     */
    public function setLabelPlacement(string $labelPlacement): ACFGroup
    {
        $this->labelPlacement = $labelPlacement;
        return $this;
    }

    /**
     * @param string $instructionPlacement
     * @return ACFGroup
     */
    public function setInstructionPlacement(string $instructionPlacement): ACFGroup
    {
        $this->instructionPlacement = $instructionPlacement;
        return $this;
    }

    /**
     * @param array $hideOnScreen
     * @return ACFGroup
     */
    public function setHideOnScreen(array $hideOnScreen): ACFGroup
    {
        $this->hideOnScreen = $hideOnScreen;
        return $this;
    }

    /**
     * @param bool $active
     * @return ACFGroup
     */
    public function setActive(bool $active): ACFGroup
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @param string $description
     * @return ACFGroup
     */
    public function setDescription(string $description): ACFGroup
    {
        $this->description = $description;
        return $this;
    }

    public function toArray()
    {
        return [
            'key' => $this->key,
            'title' => $this->title,
            'fields' => array_map(function (ACFField $field) {
                return $field->toArray();
            }, $this->fields),
            'location' => $this->location->toArray(),
            'menu_order' => $this->menuOrder,
            'position' => $this->position,
            'style' => $this->style,
            'label_placement' => $this->labelPlacement,
            'instruction_placement' => $this->instructionPlacement,
            'hide_on_screen' => empty($this->hideOnScreen) ? '' : $this->hideOnScreen,
            'active' => $this->active ? 1 : 0,
            'description' => $this->description,
        ];
    }
}
