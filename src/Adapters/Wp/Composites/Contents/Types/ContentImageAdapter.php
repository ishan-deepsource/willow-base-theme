<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials\ContentImageHyperlinkAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Models\Base\Root\Hyperlink;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * Class ImageAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class ContentImageAdapter extends AbstractContentAdapter implements ContentImageContract
{
    protected $image;

    public function __construct(array $acfArray)
    {
        parent::__construct($acfArray);
        $post = get_post(array_get($acfArray, 'file') ?? array_get($acfArray, 'image'));
        $this->image = $post ? new Image(new ImageAdapter($post)) : null;
        if (!$this->image) {
            throw new \InvalidArgumentException('Missing image.');
        }
    }

    public function isLead(): bool
    {
        return array_get($this->acfArray, 'lead_image', false);
    }

    public function getId(): ?int
    {
        return optional($this->image)->getId() ?: null;
    }

    public function getUrl(): ?string
    {
        return optional($this->image)->getUrl() ?: null;
    }

    public function getTitle(): ?string
    {
        return optional($this->image)->getTitle();
    }

    public function getDescription(): ?string
    {
        return optional($this->image)->getDescription();
    }

    public function getCaption(): ?string
    {
        return optional($this->image)->getCaption();
    }

    public function getAlt(): ?string
    {
        return optional($this->image)->getAlt();
    }

    public function getCopyright(): ?string
    {
        return optional($this->image)->getCopyright();
    }

    public function getLanguage(): ?string
    {
        return optional($this->image)->getLanguage();
    }

    public function getFocalPoint(): array
    {
        return optional($this->image)->getFocalPoint() ?? [];
    }

    public function getAspectRatio(): float
    {
        return optional($this->image)->getAspectRatio() ?? 0.0;
    }

    public function getLink(): ?HyperlinkContract
    {
        return new Hyperlink(new ContentImageHyperlinkAdapter($this, $this->acfArray));
    }

    public function getDisplayHint(): ?string
    {
        return $this->acfArray['display_hint'] ?? null;
    }
}
