<?php

namespace Bonnier\Willow\Base\Models\Base\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

class Image implements ImageContract
{
    protected $image;

    public function __construct(ImageContract $image)
    {
        $this->image = $image;
    }

    public function getId(): ?int
    {
        return $this->image->getId();
    }

    public function getUrl(): ?string
    {
        return $this->image->getUrl();
    }

    public function getTitle(): ?string
    {
        return $this->image->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->image->getDescription();
    }

    public function getCaption(): ?string
    {
        return $this->image->getCaption();
    }

    public function getLanguage(): ?string
    {
        return $this->image->getLanguage();
    }

    public function getAlt(): ?string
    {
        return $this->image->getAlt();
    }

    public function getCopyright(): ?string
    {
        return $this->image->getCopyright();
    }

    public function getFocalPoint(): array
    {
        return $this->image->getFocalPoint();
    }

    public function getAspectRatio(): float
    {
        return $this->image->getAspectRatio();
    }

    public function getLink(): ?HyperlinkContract
    {
        return $this->image->getLink();
    }
}
