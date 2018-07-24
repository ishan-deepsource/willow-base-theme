<?php

namespace Bonnier\Willow\Base\Factories;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\PageAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\AuthorAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Categories\CategoryAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Tags\TagAdapter;

/**
 * Class CompositeFactory
 *
 * @package \Bonnier\Willow\Base\Factories
 */
class WPModelFactory extends AbstractModelFactory
{
    protected $adapterMapping = [
        'wp_post' => [
            'contenthub_composite' => CompositeAdapter::class,
            'page' => PageAdapter::class,
        ],
        'wp_term' => [
            'category' => CategoryAdapter::class,
            'post_tag' => TagAdapter::class,
        ],
        'wp_user' => [
            'author'   => AuthorAdapter::class
        ]
    ];

    public function getAdapter($wpModel)
    {
        $class = null;
        if ($wpModel instanceof \WP_Post) {
            $class = collect($this->adapterMapping['wp_post'])->get($wpModel->post_type);
        }
        if ($wpModel instanceof \WP_Term) {
            $class = collect($this->adapterMapping['wp_term'])->get($wpModel->taxonomy);
        }
        if ($wpModel instanceof \WP_User) {
            $class = collect($this->adapterMapping['wp_user'])->get('author');
        }
        
        return $class;
    }
}
