<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Models\Base\Root\Hyperlink;
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
        return get_post_meta($this->getId(), '_wp_attachment_image_alt', true) ?: null;
    }

    public function getCopyright(): ?string
    {
        return get_post_meta($this->getId(), 'attachment_copyright', true) ?: null;
    }

    public function getFocalPoint(): array
    {
        if (($focalPoint = get_post_meta($this->getId(), '_focal_point', true) ?: null) &&
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
        if (($metadata = wp_get_attachment_metadata($this->getId())) &&
            isset($metadata['width']) &&
            isset($metadata['height'])
        ) {
            return $metadata['width'] / $metadata['height'];
        }
        return 0.0;
    }

    public function getLink(): ?HyperlinkContract
    {
        return new Hyperlink(new HyperlinkAdapter($this));
    }

    public function getDisplayHint(): ?string
    {
        return $this->acfArray['display_hint'] ?? null;
    }
}
