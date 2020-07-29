<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials\ContentImageHyperlinkAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ColorPaletteAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Models\Base\Root\Hyperlink;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ColorPaletteContract;
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
        if ($imageArray = array_get($acfArray, 'file') ?: array_get($acfArray, 'image')) {
            $image = WpModelRepository::instance()->getPost($imageArray);
            $this->image = new Image(new ImageAdapter($image));
        }
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

    public function getVideoUrl(): ?string
    {
        if(isset($this->acfArray['video_url'])) {
            return $this->acfArray['video_url'] ?: null;
        }
        return null;
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

    public function getColorPalette(): ?ColorPaletteContract
    {
        return optional($this->image)->getColorPalette();
    }

    public function getDisplayHint(): ?string
    {
        return $this->acfArray['display_hint'] ?? null;
    }
}
