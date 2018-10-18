<?php

namespace Bonnier\Willow\Base\Models\Base\Root;


use Bonnier\Willow\Base\Models\Contracts\Root\ColorPaletteContract;
use Illuminate\Support\Collection;

class ColorPalette implements ColorPaletteContract
{
    /**
     * @var ColorPaletteContract
     */
    private $colorPalette;

    public function __construct(ColorPaletteContract $colorPalette)
    {
        $this->colorPalette = $colorPalette;
    }

    public function getColors(): ?Collection
    {
        return $this->colorPalette->getColors();
    }

    public function getAverageLuminance(): ?float
    {
        return $this->colorPalette->getAverageLuminance();
    }

    public function getDominantColors(): ?Collection
    {
        return $this->colorPalette->getDominantColors();
    }
}