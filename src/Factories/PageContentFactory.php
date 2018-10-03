<?php

namespace Bonnier\Willow\Base\Factories;

use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types\NullContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types\TeaserListAdapter;
use Bonnier\WP\ContentHub\Editor\Helpers\AcfName;

class PageContentFactory extends AbstractModelFactory
{
    protected $adapterMapping = [
        AcfName::WIDGET_TEASER_LIST => TeaserListAdapter::class,
    ];

    public function getAdapter($model)
    {
        return collect($this->adapterMapping)->get($model['acf_fc_layout'], NullContentAdapter::class);
    }
}
