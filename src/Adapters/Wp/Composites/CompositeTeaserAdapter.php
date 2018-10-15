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

    public function getTitle(): ?string
    {
        if ($title = array_get($this->composite->getAcfFields(), $this->type . 'teaser_title')) {
            return $title;
        }

        if ($title = array_get($this->composite->getAcfFields(), 'teaser_title')) {
            return $title;
        }

        return optional($this->composite)->getTitle() ?: null;
    }

    public function getImage(): ?ImageContract
    {
        if (($imageId = array_get($this->composite->getAcfFields(), $this->type . 'teaser_image')) &&
            ($image = get_post($imageId))
        ) {
            return new Image(new ImageAdapter($image));
        }

        if (($imageId = array_get($this->composite->getAcfFields(), 'teaser_image')) &&
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

    public function getDescription(): ?string
    {
        if ($description = array_get($this->composite->getAcfFields(), $this->type . 'teaser_description')) {
            return $description;
        }

        if ($description = array_get($this->composite->getAcfFields(), 'teaser_description')) {
            return $description;
        }

        return optional($this->composite)->getDescription() ?: null;
    }
}
