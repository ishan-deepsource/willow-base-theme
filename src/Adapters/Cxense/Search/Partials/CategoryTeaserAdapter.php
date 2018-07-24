<?php

namespace Bonnier\Willow\Base\Adapters\Cxense\Search\Partials;

use Bonnier\Willow\Base\Adapters\Cxense\Search\CategoryAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\AbstractTeaserAdapter;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

class CategoryTeaserAdapter extends AbstractTeaserAdapter
{
    protected $category;

    /**
     * CategoryTeaserAdapter constructor.
     * @param CategoryAdapter $category
     * @param string $type
     */
    public function __construct(CategoryAdapter $category, string $type)
    {
        parent::__construct($type);
        $this->category = $category;
    }

    public function getTitle(): string
    {
        return $this->category->getName() ?? '';
    }

    public function getImage(): ?ImageContract
    {
        return $this->category->getImage();
    }

    public function getDescription(): string
    {
        return $this->category->getDescription() ?? '';
    }
}
