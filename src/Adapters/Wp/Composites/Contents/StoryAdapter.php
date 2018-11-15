<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\StoryContract;
use Illuminate\Support\Collection;

class StoryAdapter implements StoryContract
{
    protected $composite;
    protected $meta;

    public function __construct(\WP_Post $composite, ?array $meta)
    {
        $this->composite = $composite;
        $this->meta = $meta;
    }

    public function getTeaser(): ?CompositeContract
    {
        return new Composite(new CompositeAdapter($this->composite, $this->meta));
    }

    public function getArticles(): ?Collection
    {
        $articles = collect(get_field('composite_content', $this->composite->ID) ?? [])
            ->reduce(function (Collection $articles, array $content) {
                if (array_get($content, 'acf_fc_layout') === 'associated_composites') {
                    return $articles->merge(collect(array_get($content, 'composites', []))
                        ->map(function (\WP_Post $composite) {
                            $meta = get_post_meta($composite->ID);
                            return new Composite(new CompositeAdapter($composite, $meta));
                        }));
                }
                return $articles;
            }, new Collection());

        return $articles->isNotEmpty() ? $articles : null;
    }
}
