<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\InfoBoxContract;

/**
 * Class Link
 *
 * @property InfoBoxContract $model
 *
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 */
class InfoBox extends AbstractContent implements InfoBoxContract
{
    /**
     * Link constructor.
     *
     * @param \Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\InfoBoxContract $infoBox
     */
    public function __construct(InfoBoxContract $infoBox)
    {
        parent::__construct($infoBox);
    }

    public function getBody(): ?string
    {
        return $this->model->getBody();
    }

    public function getTitle(): ?string
    {
        return $this->model->getTitle();
    }

    public function getDisplayHint(): string
    {
        return $this->model->getDisplayHint();
    }

    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }
}
