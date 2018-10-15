<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\HotspotItemContract;

/**
 * Class GalleryAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class HotspotItemAdapter implements HotspotItemContract
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
        return array_get($this->acfArray, 'title') ?: null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->acfArray, 'description') ?: null;
    }

    public function getCoordinates(): ?array
    {
        if (($focalPoint = array_get($this->acfArray, 'coordinates')) &&
            !empty($coords = explode(',', $focalPoint)) &&
            count($coords) == 2) {
            return [
                'x' => floatval($coords[0]),
                'y' => floatval($coords[1])
            ];
        }
        return null;
    }
}
