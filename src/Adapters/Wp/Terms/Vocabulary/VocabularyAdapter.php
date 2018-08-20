<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Terms\Vocabulary;

use Bonnier\Willow\Base\Adapters\Wp\AbstractWpAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\BrandAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Tags\TagAdapter;
use Bonnier\Willow\Base\Models\Base\Root\Brand;
use Bonnier\Willow\Base\Models\Base\Terms\Tag;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Terms\VocabularyContract;
use Bonnier\Willow\Base\Models\Contracts\Root\BrandContract;
use Illuminate\Support\Collection;

/**
 * Class VocabularyAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp\Terms\Vocabulary
 */
class VocabularyAdapter extends AbstractWpAdapter implements VocabularyContract
{
    protected $vocabulary;
    protected $composite;

    public function __construct(CompositeContract $composite,$vocabulary)
    {
        $this->vocabulary = $vocabulary;
        $this->composite = $composite;
    }

    public function getName(): ?string
    {
        return $this->vocabulary->name ?? null;
    }

    public function getMachineName(): ?string
    {
        return $this->vocabulary->machine_name ?? null;
    }

    public function getContentHubId(): ?string
    {
        return $this->vocabulary->content_hub_id ?? null;
    }

    public function getMultiSelect(): ?string
    {
        return $this->vocabulary->multi_select ?? null;
    }

    public function getBrand(): ?BrandContract
    {
        return $this->vocabulary ? new Brand(new BrandAdapter($this->vocabulary->brand)) : null;
    }

    public function getTerms(): ?Collection{
        //If it's possible to select multiple, we need to run through each item
        return $this->vocabulary ? new Collection(collect(wp_get_post_terms($this->composite->getId(), $this->vocabulary->machine_name))->map(function (\WP_Term $tag) {
            return new Tag(new TagAdapter($tag));
        })->toArray()) : collect([]);
    }
}
