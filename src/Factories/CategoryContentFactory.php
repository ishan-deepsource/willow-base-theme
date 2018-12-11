<?php

namespace Bonnier\Willow\Base\Factories;

use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types\BannerPlacementAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types\CommercialSpotAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types\FeaturedContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types\NewsletterAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types\NullContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types\SeoTextAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types\TaxonomyListAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types\TeaserListAdapter;
use Bonnier\WP\ContentHub\Editor\Helpers\AcfName;

class CategoryContentFactory extends AbstractModelFactory
{
    protected $adapterMapping = [
        AcfName::WIDGET_TEASER_LIST => TeaserListAdapter::class,
        AcfName::WIDGET_FEATURED_CONTENT => FeaturedContentAdapter::class,
        AcfName::WIDGET_SEO_TEXT => SeoTextAdapter::class,
        AcfName::WIDGET_NEWSLETTER => NewsletterAdapter::class,
        AcfName::WIDGET_BANNER_PLACEMENT => BannerPlacementAdapter::class,
        AcfName::WIDGET_TAXONOMY_TEASER_LIST => TaxonomyListAdapter::class,
        AcfName::WIDGET_COMMERCIAL_SPOT => CommercialSpotAdapter::class,
    ];

    public function getAdapter($model)
    {
        return collect($this->adapterMapping)->get($model['acf_fc_layout'], NullContentAdapter::class);
    }
}
