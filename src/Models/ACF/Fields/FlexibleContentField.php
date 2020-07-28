<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;
use Bonnier\Willow\Base\Models\ACF\ACFLayout;

class FlexibleContentField extends ACFField
{
    public const TYPE = 'flexible_content';
    private $layouts = [];
    private $buttonLabel = '';
    private $min = '';
    private $max = '';

    /**
     * @param array $layouts
     * @return FlexibleContentField
     */
    public function setLayouts(array $layouts): FlexibleContentField
    {
        $this->layouts = $layouts;
        return $this;
    }

    public function addLayout(ACFLayout $layout)
    {
        $this->layouts[$layout->getKey()] = $layout;
    }

    public function removeLayout(string $layoutKey)
    {
        unset($this->layouts[$layoutKey]);
        return $this;
    }

    /**
     * @param string $buttonLabel
     * @return FlexibleContentField
     */
    public function setButtonLabel(string $buttonLabel): FlexibleContentField
    {
        $this->buttonLabel = $buttonLabel;
        return $this;
    }

    /**
     * @param string $min
     * @return FlexibleContentField
     */
    public function setMin(string $min): FlexibleContentField
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @param string $max
     * @return FlexibleContentField
     */
    public function setMax(string $max): FlexibleContentField
    {
        $this->max = $max;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'layouts' => array_map(function (ACFLayout $layout) {
                return $layout->toArray();
            }, $this->layouts),
            'button_label' => $this->buttonLabel,
            'min' => $this->min,
            'max' => $this->max,
        ]);
    }
}
