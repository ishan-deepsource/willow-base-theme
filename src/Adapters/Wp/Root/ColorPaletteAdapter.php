<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Actions\Backend\AddMedia;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Helpers\ImgixHelper;
use Bonnier\Willow\Base\Models\Contracts\Root\ColorPaletteContract;
use Illuminate\Support\Collection;

class ColorPaletteAdapter implements ColorPaletteContract
{
    const COLOR_PALETTE_META = 'imgix_palette';

    private $rawPalette;

    public function __construct($attachmentId)
    {
        $meta = WpModelRepository::instance()->getPostMeta($attachmentId);
        $this->rawPalette = array_get($meta, sprintf('%s.0', self::COLOR_PALETTE_META));

        if (!$this->rawPalette && $imageUrl = wp_get_attachment_url($attachmentId)) {
            $this->rawPalette = ImgixHelper::getColorPalette($imageUrl);
            update_post_meta($attachmentId, self::COLOR_PALETTE_META, $this->rawPalette);
        }

        // WordPress might have saved a serialized meta field
        // and will then return an object instead of a json encoded string.
        if (is_string($this->rawPalette)) {
            $this->rawPalette = json_decode($this->rawPalette);
        }
    }

    public function getColors(): ?Collection
    {
        // Only output the hex values
        if (isset($this->rawPalette->colors)) {
            return collect($this->rawPalette->colors)->pluck('hex');
        }

        return null;
    }

    public function getAverageLuminance(): ?float
    {
        // Only output the hex values
        if (isset($this->rawPalette->average_luminance)) {
            return $this->rawPalette->average_luminance;
        }

        return null;
    }

    public function getDominantColors(): ?Collection
    {
        if (isset($this->rawPalette->dominant_colors)) {
            return collect($this->rawPalette->dominant_colors)->map(function ($var) {
                return $var->hex;
            });
        }

        return null;
    }
}
