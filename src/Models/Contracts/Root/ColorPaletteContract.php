<?php

namespace Bonnier\Willow\Base\Models\Contracts\Root;

use Illuminate\Support\Collection;

interface ColorPaletteContract
{
    public function getColors(): Collection;

    public function getAverageLuminance(): float;

    public function getDominantColors(): Collection;
}