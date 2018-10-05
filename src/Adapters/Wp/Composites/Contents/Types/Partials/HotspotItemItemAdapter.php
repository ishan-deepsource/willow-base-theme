<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\HotspotItemContract;

/**
 * Class GalleryAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class HotspotItemItemAdapter implements HotspotItemContract
{
    protected $acfArray;

    /**
     * ImageHotspotAdapter constructor.
     * @param $acfArray
     */
    public function __construct($acfArray)
    {
        $this->acfArray = $acfArray;
    }

    public function getTitle(): ?string
    {
        return array_get($this->acfArray, 'title', null) ?: null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->acfArray, 'description', null) ?: null;
    }

    public function getCoordinates(): array
    {
        if (($focalPoint = array_get($this->acfArray, 'coordinates') ?: null) &&
            !empty($coords = explode(',', $focalPoint)) &&
            count($coords) == 2) {
            return [
                'x' => $coords[0],
                'y' => $coords[1]
            ];
        }

        return [
            'x' => 0.5,
            'y' => 0.5
        ];
    }
}
