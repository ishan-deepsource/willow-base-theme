<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\ColorPaletteContract;
use League\Fractal\TransformerAbstract;

class ColorPaletteTransformer extends TransformerAbstract
{
    public function transform(ColorPaletteContract $colorPalette)
    {
        return [
            'colors'            => $colorPalette->getColors(),
            'average_luminance' => $colorPalette->getAverageLuminance(),
            'dominant_colors'   => $colorPalette->getDominantColors(),
        ];
    }
}