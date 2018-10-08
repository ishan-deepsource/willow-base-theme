<?php

namespace Bonnier\Willow\Base\Adapters\Wp\App\Partials;

use Bonnier\Willow\Base\Adapters\Wp\App\InstagramCompositeAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\AbstractTeaserAdapter;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

class SocialFeedTeaserAdapter extends AbstractTeaserAdapter
{
    protected $adapter;

    public function __construct(CompositeContract $adapter, string $type)
    {
        parent::__construct($type);
        $this->adapter = $adapter;
    }

    public function getTitle(): ?string
    {
        return optional($this->adapter)->getTitle() ?: null;
    }

    public function getImage(): ?ImageContract
    {
        return $this->adapter->getLeadImage();
    }

    public function getDescription(): ?string
    {
        return optional($this->adapter)->getDescription() ?: null;
    }
}
