<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Factories\TaxonomyFactory;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Base\Terms\Category;
use Bonnier\Willow\Base\Models\Base\Terms\Tag;
use Bonnier\Willow\Base\Models\Base\Terms\Vocabulary;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\TaxonomyListContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\WP\ContentHub\Editor\Helpers\AcfName;
use Bonnier\WP\ContentHub\Editor\Models\WpTaxonomy;
use Illuminate\Support\Collection;

/**
 * Class TaxonomyListAdapter
 * @package Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types
 */
class TaxonomyListAdapter extends AbstractContentAdapter implements TaxonomyListContract
{
    protected $taxonomyMap = [
        AcfName::FIELD_CATEGORY => Category::class,
        AcfName::FIELD_TAG => Tag::class,
    ];
    protected $taxonomyFactory;

    public function __construct(array $acfArray)
    {
        parent::__construct($acfArray);
        WpTaxonomy::get_custom_taxonomies()->each(function ($taxonomy) {
            $this->taxonomyMap[$taxonomy->machine_name] = Tag::class;
        });
    }

    public function getTitle(): ?string
    {
        return array_get($this->acfArray, AcfName::FIELD_TITLE) ?: null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->acfArray, AcfName::FIELD_DESCRIPTION) ?: null;
    }

    public function getImage(): ?ImageContract
    {
        if (($imageId = array_get($this->acfArray, AcfName::FIELD_IMAGE)) && $image = get_post($imageId)) {
            return new Image(new ImageAdapter($image));
        }

        return null;
    }

    public function getLabel(): ?string
    {
        return array_get($this->acfArray, AcfName::FIELD_LABEL) ?: null;
    }

    public function getDisplayHint(): ?string
    {
        return array_get($this->acfArray, AcfName::FIELD_DISPLAY_HINT) ?: null;
    }

    public function getTaxonomy(): ?string
    {
        return array_get($this->acfArray, AcfName::FIELD_TAXONOMY) ?: null;
    }

    public function getTaxonomyList(): ?Collection
    {
        if ($taxonomies = array_get($this->acfArray, $this->getTaxonomy())) {
            return collect($taxonomies)->map(function (int $termId) {
                if ($taxonomy = get_term($termId)) {
                    $class = collect($this->taxonomyMap)->get($this->getTaxonomy());
                    try {
                        return $this->getTaxonomyFactory($class)->getModel($taxonomy);
                    } catch (\InvalidArgumentException $exception) {
                        return null;
                    }
                }
                return null;
            })->reject(function ($taxonomy) {
                return is_null($taxonomy);
            });
        }

        return null;
    }

    private function getTaxonomyFactory(string $class)
    {
        if ($this->taxonomyFactory) {
            return $this->taxonomyFactory->setBaseClass($class);
        }

        return $this->taxonomyFactory = new TaxonomyFactory($class);
    }
}
