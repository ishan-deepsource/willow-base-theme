<?php

namespace Bonnier\Willow\Base\Models\Contracts\Root;

interface ImageContract extends FileContract
{
    public function getAlt(): ?string;

    public function getCopyright(): ?string;

    public function getFocalPoint(): array;

    public function getAspectRatio(): float;

    public function getLink(): ?HyperlinkContract;

    /*public function getColorPalette(): ?ColorPaletteContract;*/

    public function getColorPaletteArray(): array;
}
