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

    public function __construct($attachmentId)
    {
        $meta = WpModelRepository::instance()->getPostMeta($attachmentId);
        $paletteString = array_get($meta, sprintf('%s.0', self::COLOR_PALETTE_META));
        $unserialized = $paletteString;
        $counter = 0;
        while (is_serialized_string($unserialized)) {
            $unserialized = unserialize($unserialized);
            $this->colorPalette = $unserialized;
            $counter++;
            if ($counter > 0) {
                $imageUrl = wp_get_attachment_url($attachmentId);
                $this->colorPalette = ImgixHelper::getColorPalette($imageUrl);
                update_post_meta($attachmentId, self::COLOR_PALETTE_META, $this->colorPalette);
                break;
            }
        }

        if (is_string($paletteString)) {
            $palette = json_decode($paletteString);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->colorPalette = $palette;
            }
        }

        if (!$this->colorPalette && $imageUrl = wp_get_attachment_url($attachmentId)) {
            $this->colorPalette = ImgixHelper::getColorPalette($imageUrl);
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
}
