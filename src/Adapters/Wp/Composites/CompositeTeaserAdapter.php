<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites;

use Bonnier\Willow\Base\Adapters\Wp\Root\AbstractTeaserAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

class CompositeTeaserAdapter extends AbstractTeaserAdapter
{
    protected $composite;

    public function __construct(CompositeAdapter $composite, string $type)
    {
        $this->composite = $composite;
        parent::__construct($type);
    }

    public function getTitle(): string
    {
        if ($title = $this->composite->getAcfFields()[$this->type . 'teaser_title'] ?? null) {
            return $title;
        }

        if ($title = $this->composite->getAcfFields()['teaser_title'] ?? null) {
            return $title;
        }

        return $this->composite->getTitle() ?? '';
    }

    public function getImage(): ?ImageContract
    {
        if (($imageId = $this->composite->getAcfFields()[$this->type . 'teaser_image'] ?? null) &&
            ($image = get_post($imageId))
        ) {
            return new Image(new ImageAdapter($image));
        }

        if (($imageId = $this->composite->getAcfFields()['teaser_image'] ?? null) &&
            ($image = get_post($imageId))
        ) {
            return new Image(new ImageAdapter($image));
        }

        if ($leadImage = $this->composite->getLeadImage()) {
            return $leadImage;
        }

        if ($firstInlineImage = $this->composite->getFirstInlineImage()) {
            return $firstInlineImage;
        }

        if ($firstFileImage = $this->composite->getFirstFileImage()) {
            return $firstFileImage;
        }

        return null;
    }

    public function getDescription(): string
    {
        if ($description = $this->composite->getAcfFields()[$this->type . 'teaser_description'] ?? null) {
            return $description;
        }

        if ($description = $this->composite->getAcfFields()['teaser_description'] ?? null) {
            return $description;
        }

        return $this->composite->getDescription() ?? '';
    }
}
