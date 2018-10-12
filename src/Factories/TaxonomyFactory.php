<?php

namespace Bonnier\Willow\Base\Factories;

use Bonnier\Willow\Base\Adapters\Wp\Terms\Categories\CategoryAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\NullTaxonomyAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Tags\TagAdapter;
use Bonnier\WP\ContentHub\Editor\Helpers\AcfName;
use Bonnier\WP\ContentHub\Editor\Models\WpTaxonomy;

class TaxonomyFactory extends AbstractModelFactory
{
    protected $adapterMapping = [
        AcfName::TAXONOMY_CATEGORY => CategoryAdapter::class,
        AcfName::TAXONOMY_TAG => TagAdapter::class,
    ];

    public function __construct($baseClass)
    {
        parent::__construct($baseClass);
        WpTaxonomy::get_custom_taxonomies()->each(function ($taxonomy) {
            $this->adapterMapping[$taxonomy->machine_name] = TagAdapter::class;
        });
    }

    /**
     * @param \WP_Term $model
     * @return mixed
     */
    public function getAdapter($model)
    {
        return collect($this->adapterMapping)
            ->get($model->taxonomy, NullTaxonomyAdapter::class);
    }
}
