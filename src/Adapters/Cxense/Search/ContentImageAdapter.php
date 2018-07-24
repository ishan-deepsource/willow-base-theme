<?php

namespace Bonnier\Willow\Base\Adapters\Cxense\Search;

use Bonnier\WP\Cxense\Parsers\Document;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

/**
 * Class DocumentAdapter
 *
 * @package \\${NAMESPACE}
 */
class ContentImageAdapter implements ContentImageContract
{
    protected $url;

    public function __construct(Document $document)
    {
        $this->url = $document->getField('dominantimage');
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
        return false;
    }
}
