<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;


use Bonnier\Willow\Base\Models\Contracts\Root\ColorPaletteContract;
use Illuminate\Support\Collection;

class ColorPaletteAdapter implements ColorPaletteContract
{
    const COLOR_PALETTE_META = 'imgix_palette';

    private $rawPalette;

    public function __construct($attachmentId)
    {
        $this->rawPalette = get_post_meta($attachmentId, static::COLOR_PALETTE_META, true);

        if (!$this->rawPalette ) {
            // Get imgix color palette and save it in attachment metadata
            $this->rawPalette  = file_get_contents(wp_get_attachment_url($attachmentId) . '?palette=json');
            update_post_meta($attachmentId, static::COLOR_PALETTE_META, $this->rawPalette);
        }

        $this->rawPalette = json_decode($this->rawPalette);
    }

    public function getColors(): Collection
    {
        // Only output the hex values
        if (isset($this->rawPalette->colors)) {
            return collect($this->rawPalette->colors)->pluck('hex');
        }
    }

    public function getAverageLuminance(): float
    {
        // Only output the hex values
        if (isset($this->rawPalette->average_luminance)) {
            return $this->rawPalette->average_luminance;
        }
    }

    public function getDominantColors(): Collection
    {
        if (isset($this->rawPalette->dominant_colors)) {
            return collect($this->rawPalette->dominant_colors)->map(function ($var) {
                return $var->hex;
            });
        }
    }
}