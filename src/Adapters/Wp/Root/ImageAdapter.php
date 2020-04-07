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
        if (defined('AWS_S3_DOMAIN') &&
            ($s3Meta = array_get($this->postMeta, 'amazonS3_info.0')) &&
            $s3Data = unserialize($s3Meta)
        ) {
            return sprintf('https://%s/%s', AWS_S3_DOMAIN, array_get($s3Data, 'key'));
        }

        if ($this->file instanceof \WP_Post) {
            return wp_get_attachment_image_url($this->getId(), 'original') ?: null;
        } else {
            return array_get($this->file, 'url') ?: null;
        }
    }

    public function getAlt(): ?string
    {
        return array_get($this->file, 'alt', data_get($this->file, 'alt')) ?: null;
    }

    public function getCopyright(): ?string
    {
        return array_get($this->postMeta, 'attachment_copyright.0') ?: null;
    }

    public function getFocalPoint(): array
    {
        if (($focalPoint = array_get($this->postMeta, '_focal_point.0') ?: null) &&
            !empty($coords = explode(',', $focalPoint)) &&
            count($coords) == 2) {
            return [
                'x' => floatval($coords[0]),
                'y' => floatval($coords[1]),
            ];
        }

        return [
            'x' => floatval(0.5),
            'y' => floatval(0.5)
        ];
    }

    public function getAspectRatio(): float
    {
        $width = array_get($this->attachmentMeta, 'width');
        $height = array_get($this->attachmentMeta, 'height');
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
