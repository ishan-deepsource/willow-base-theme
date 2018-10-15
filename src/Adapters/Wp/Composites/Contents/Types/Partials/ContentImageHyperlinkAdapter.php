<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\ContentImageAdapter;
use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;

class ContentImageHyperlinkAdapter implements HyperlinkContract
{
    protected $image;
    protected $acfArray;

    /**
     * ContentImageHyperlinkAdapter constructor.
     * @param ContentImageAdapter $image
     * @param $acfArray
     */
    public function __construct(ContentImageAdapter $image, $acfArray)
    {
        $this->image = $image;
        $this->acfArray = $acfArray;
    }


    public function getTitle(): ?string
    {
        return optional($this->image)->getTitle() ?: null;
    }

    public function getUrl(): ?string
    {
        return array_get($this->acfArray, 'link') ?: null;
    }

    public function getRelationship(): ?string
    {
        return array_get($this->acfArray ,'rel') ?: null;
    }

    public function getTarget(): ?string
    {
        return array_get($this->acfArray ,'target') ?: null;
    }
}
