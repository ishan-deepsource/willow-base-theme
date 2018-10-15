<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentAudioContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\HotspotItemContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Illuminate\Support\Collection;

/**
 * Class File
 *
 * @property ContentAudioContract $model
 *
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 */
class HotspotItem implements HotspotItemContract
{
    protected $model;

    /**
     * File constructor.
     *
     * @param \Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\HotspotItemContract $hotspot
     */
    public function __construct(HotspotItemContract $hotspot)
    {
        $this->model = $hotspot;
    }

    public function getTitle(): ?string
    {
        return $this->model->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->model->getDescription();
    }

    public function getCoordinates(): ?array
    {
        return $this->model->getCoordinates();
    }
}
