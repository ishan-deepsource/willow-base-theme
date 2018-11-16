<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Terms\Vocabulary;

use Bonnier\Willow\Base\Adapters\Wp\AbstractWpAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\BrandAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Tags\TagAdapter;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
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
class VocabularyAdapter implements VocabularyContract
{
    protected $vocabulary;
    protected $composite;

    public function __construct(CompositeContract $composite, $vocabulary)
    {
        $this->vocabulary = $vocabulary;
        $this->composite = $composite;
    }

    public function getName(): ?string
    {
        return data_get($this->vocabulary, 'name') ?: null;
    }

    public function getMachineName(): ?string
    {
        return data_get($this->vocabulary, 'machine_name') ?: null;
    }

    public function getContentHubId(): ?string
    {
        return data_get($this->vocabulary, 'content_hub_id') ?: null;
    }

    public function getMultiSelect(): ?string
    {
        return data_get($this->vocabulary, 'multi_select') ?: null;
    }

    public function getTerms(): ?Collection
    {
        if ($machineName = $this->getMachineName()) {
            $term = wp_get_post_terms($this->composite->getId(), $machineName);
            return collect($term)->map(function (\WP_Term $term) {
                $tag = WpModelRepository::instance()->getTerm($term);
                return new Tag(new TagAdapter($tag));
            });
        }

        return null;
    }
}
