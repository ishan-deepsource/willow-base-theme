<?php

namespace Bonnier\Willow\Base\Models\ACF\Fields;

use Bonnier\Willow\Base\Models\ACF\ACFField;

class TabField extends ACFField
{
    public const TYPE = 'tab';

    private $placement = 'top';
    private $endpoint = 0;

    /**
     * @param string $placement
     * @return TabField
     */
    public function setPlacement(string $placement): TabField
    {
        $this->placement = $placement;
        return $this;
    }

    /**
     * @param int $endpoint
     * @return TabField
     */
    public function setEndpoint(int $endpoint): TabField
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'placement' => $this->placement,
            'endpoint' => $this->endpoint,
        ]);
    }
}
