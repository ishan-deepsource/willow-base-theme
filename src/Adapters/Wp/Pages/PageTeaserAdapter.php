<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Pages;

use Bonnier\Willow\Base\Adapters\Wp\Root\AbstractTeaserAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

class PageTeaserAdapter extends AbstractTeaserAdapter
{
    protected $page;

    public function __construct(PageAdapter $page, $type)
    {
        $this->page = $page;
        parent::__construct($type);
    }

    public function getTitle(): string
    {
        if ($title = $this->page->getAcfFields()[$this->type . 'teaser_title'] ?? null) {
            return $title;
        }

        if ($title = $this->page->getAcfFields()['teaser_title'] ?? null) {
            return $title;
        }

        return $this->page->getTitle() ?? '';
    }

    public function getImage(): ?ImageContract
    {
        if (($imageId = $this->page->getAcfFields()[$this->type . 'teaser_image'] ?? null) &&
            ($image = get_post($imageId))
        ) {
            return new Image(new ImageAdapter($image));
        }

        if (($imageId = $this->page->getAcfFields()['teaser_image'] ?? null) &&
            ($image = get_post($imageId))
        ) {
            return new Image(new ImageAdapter($image));
        }

        return null;
    }

    public function getDescription(): string
    {
        if ($description = $this->page->getAcfFields()[$this->type . 'teaser_description'] ?? null) {
            return $description;
        }

        if ($description = $this->page->getAcfFields()['teaser_description'] ?? null) {
            return $description;
        }

        return '';
    }
}
