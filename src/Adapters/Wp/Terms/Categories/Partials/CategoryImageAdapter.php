<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Terms\Categories\Partials;

use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

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
        return $this->meta->image_url->{pll_current_language()} ?? null;
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
        return ['x' => 0.5, 'y' => 0.5];
    }
    
    public function getAspectRatio(): float
    {
        return 0.0;
    }
}
