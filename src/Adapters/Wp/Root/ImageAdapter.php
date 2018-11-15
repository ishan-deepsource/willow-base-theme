<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Models\Base\Root\ColorPalette;
use Bonnier\Willow\Base\Models\Base\Root\Hyperlink;
use Bonnier\Willow\Base\Models\Contracts\Root\ColorPaletteContract;
use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

/**
 * Class ImageAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class ImageAdapter extends FileAdapter implements ImageContract
{
    public function getUrl(): ?string
    {
        return wp_get_attachment_image_url($this->getId(), 'original') ?: null;
    }

    public function getAlt(): ?string
    {
        return array_get($this->meta, '_wp_attachment_image_alt') ?: null;
    }

    public function getCopyright(): ?string
    {
        return array_get($this->meta, 'attachment_copyright') ?: null;
    }

    public function getFocalPoint(): array
    {
        if (($focalPoint = array_get($this->meta, '_focal_point') ?: null) &&
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

    public function getAspectRatio(): float
    {
        $width = array_get($this->meta, 'width');
        $height = array_get($this->meta, 'height');
        if ($width && $height) {
            return $width / $height;
        }
        return 0.0;
    }

    public function getLink(): ?HyperlinkContract
    {
        return null;
    }

    public function getDisplayHint(): ?string
    {
        return null;
    }

    public function getColorPalette(): ?ColorPaletteContract
    {
        return new ColorPalette(new ColorPaletteAdapter($this->getId()));
    }
}
