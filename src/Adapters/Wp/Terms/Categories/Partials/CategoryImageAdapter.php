<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Terms\Categories\Partials;

use Bonnier\Willow\Base\Models\Contracts\Root\ColorPaletteContract;
use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;

/**
 * Class CategoryImageAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp\Terms\Categories\Partials
 */
class CategoryImageAdapter implements ImageContract
{
    protected $meta;

    public function __construct($categoryMeta)
    {
        $this->meta = $categoryMeta;
    }

    public function getId(): ?int
    {
        return null;
    }

    public function getUrl(): ?string
    {
        return data_get($this->meta, 'image_url.0') ?: null;
    }

    public function getTitle(): ?string
    {
        return null;
    }

    public function getDescription(): ?string
    {
        return null;
    }

    public function getCaption(): ?string
    {
        return null;
    }

    public function getLanguage(): ?string
    {
        return null;
    }

    public function getAlt(): ?string
    {
        return null;
    }

    public function getCopyright(): ?string
    {
        return null;
    }

    public function getFocalPoint(): array
    {
        return [
            'x' => floatval(0.5),
            'y' => floatval(0.5)
        ];
    }

    public function getAspectRatio(): float
    {
        return floatval(0.0);
    }

    public function getLink(): ?HyperlinkContract
    {
        return null;
    }

    public function getColorPalette(): ?ColorPaletteContract
    {
        return null;
    }
}
