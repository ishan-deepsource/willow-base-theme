<?php

namespace Bonnier\Willow\Base\Adapters\Wp\App\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;

class SocialFeedImageAdapter implements ContentImageContract
{
    protected $url;

    public function __construct($url)
    {
        $this->url = $url;
    }


    public function getType(): string
    {
        return 'image';
    }

    public function isLocked(): bool
    {
        return false;
    }

    public function getStickToNext(): bool
    {
        return false;
    }

    public function isLead(): bool
    {
        return true;
    }

    public function getId(): ?int
    {
        return null;
    }

    public function getUrl(): ?string
    {
        return $this->url;
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
            'x' => 0.5,
            'y' => 0.5
        ];
    }

    public function getAspectRatio(): float
    {
        return 0.0;
    }

    public function getLink(): ?HyperlinkContract
    {
        return null;
    }
}
