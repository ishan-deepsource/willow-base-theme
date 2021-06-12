<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
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
        $this->meta = WpModelRepository::instance()->getPostMeta($this->composite);
    }

    public function getTeaser(): ?CompositeContract
    {
        return new Composite(new CompositeAdapter($this->composite));
    }

    public function getArticles(): ?Collection
    {
        $postMetaData = get_post_meta($this->composite->ID);
        $allArticles = collect(WpModelRepository::instance()->getAcfField($this->composite->ID, 'composite_content') ?? []);
        $associatedComposites = [];
        foreach ($allArticles as $key => $item) {
            if (array_get($item, 'acf_fc_layout') === 'associated_composites') {
                if ($postMetaData["composite_content_{$key}_display_hint"][0] === 'story-list') {
                    $associatedComposites = [$item];
                    break;
                }
                $associatedComposites[] = $item;
            }
        }
        $articles = collect($associatedComposites)
            ->reduce(function (Collection $articles, array $content) {
                if (array_get($content, 'acf_fc_layout') === 'associated_composites') {
                    return $articles->merge(collect(array_get($content, 'composites', []))
                        ->map(function (\WP_Post $post) {
                            $composite = WpModelRepository::instance()->getPost($post);
                            return new Composite(new CompositeAdapter($composite));
                        }));
                }
                return $articles;
            }, new Collection());

        return $articles->isNotEmpty() ? $articles : null;
    }
}
