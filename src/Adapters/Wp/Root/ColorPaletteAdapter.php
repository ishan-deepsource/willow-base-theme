<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Helpers\ImgixHelper;
use Bonnier\Willow\Base\Models\Contracts\Root\ColorPaletteContract;
use Illuminate\Support\Collection;

class ColorPaletteAdapter implements ColorPaletteContract
{
    const COLOR_PALETTE_META = 'imgix_palette';

    private $colorPalette;
    private $updatePostMeta = false;

    public function __construct($attachmentId)
    {
        $meta = WpModelRepository::instance()->getPostMeta($attachmentId);
        $this->colorPalette = array_get($meta, sprintf('%s.0', self::COLOR_PALETTE_META));

        $this->colorPalette = $this->unserializeRecursive($this->colorPalette);

        if (!$this->getColors() && $imageUrl = wp_get_attachment_url($attachmentId)) {
            $this->colorPalette = ImgixHelper::getColorPalette($imageUrl);
            $this->updatePostMeta = true;

        }

        if ($this->updatePostMeta) {
            update_post_meta($attachmentId, self::COLOR_PALETTE_META, $this->colorPalette);
        }
    }

    public function getColors(): ?Collection
    {
        // Only output the hex values
        if (isset($this->colorPalette->colors)) {
            return collect($this->colorPalette->colors)->pluck('hex');
        }

        return null;
    }

    public function getAverageLuminance(): ?float
    {
        // Only output the hex values
        if (isset($this->colorPalette->average_luminance)) {
            return $this->colorPalette->average_luminance;
        }

        return null;
    }

    public function getDominantColors(): ?Collection
    {
        if (isset($this->colorPalette->dominant_colors)) {
            return collect($this->colorPalette->dominant_colors)->map(function ($var) {
                return $var->hex;
            });
        }

        return null;
    }


    private function unserializeRecursive($data, $counter = 0)
    {
        if (is_serialized($data)) {
            // Suppress notice as false is returned in case of failure
            $data = @unserialize($data);
            $counter++;
            return $this->unserializeRecursive($data, $counter);

        }
        if ($data && ($counter > 1)) {
            $this->updatePostMeta = true;
        }
        return $data;
    }
}
