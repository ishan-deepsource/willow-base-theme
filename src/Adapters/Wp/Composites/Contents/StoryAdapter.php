<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Factories\DataFactory;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\StoryContract;
use Illuminate\Support\Collection;

class StoryAdapter implements StoryContract
{
    protected $composite;
    protected $meta;

    public function __construct(\WP_Post $composite)
    {
        $this->composite = $composite;
        $this->meta = DataFactory::instance()->getPostMeta($this->composite);
    }

    public function getTeaser(): ?CompositeContract
    {
        return new Composite(new CompositeAdapter($this->composite));
    }

    public function getArticles(): ?Collection
    {
        $articles = collect(DataFactory::instance()->getAcfField($this->composite->ID, 'composite_content') ?? [])
            ->reduce(function (Collection $articles, array $content) {
                if (array_get($content, 'acf_fc_layout') === 'associated_composites') {
                    return $articles->merge(collect(array_get($content, 'composites', []))
                        ->map(function (\WP_Post $post) {
                            $composite = DataFactory::instance()->getPost($post);
                            return new Composite(new CompositeAdapter($composite));
                        }));
                }
                return $articles;
            }, new Collection());

        return $articles->isNotEmpty() ? $articles : null;
    }
}
