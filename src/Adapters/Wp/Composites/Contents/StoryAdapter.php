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

    public function __construct(\WP_Post $composite)
    {
        $this->composite = $composite;
    }

    public function getTeaser(): ?CompositeContract
    {
        return new Composite(new CompositeAdapter($this->composite));
    }

    public function getArticles(): ?Collection
    {
        $articles = collect(get_field('composite_content', $this->composite->ID) ?? [])
            ->map(function ($content) {
                if (array_get($content, 'acf_fc_layout') === 'associated_composite' &&
                    ($composite = array_get($content, 'composite.0')) &&
                    $composite instanceof \WP_Post) {
                    return new Composite(new CompositeAdapter($composite));
                }
                return null;
            })
            ->reject(function ($content) {
                return is_null($content);
            });

        return $articles->isNotEmpty() ? $articles : null;
    }
}
