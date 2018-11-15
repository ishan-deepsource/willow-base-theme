<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Factories\DataFactory;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\ParagraphListItemContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

class ParagraphListItemAdapter implements ParagraphListItemContract
{
    private $item;

    public function __construct($item)
    {
        $this->item = $item;
    }

    public function getTitle(): ?string
    {
        return array_get($this->item, 'title') ?: null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->item, 'description') ?: null;
    }

    public function getImage(): ?ImageContract
    {
        if ($imageArray = array_get($this->item, 'image')) {
            $image = DataFactory::instance()->getPost($imageArray);
            return new Image(new ImageAdapter($image));
        }

        return null;
    }
}
