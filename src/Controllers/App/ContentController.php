<?php

namespace Bonnier\Willow\Base\Controllers\App;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Helpers\SortBy;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;

class ContentController extends BaseController
{
    public function register_routes()
    {
        register_rest_route('app/content', '/popular', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, 'popular']
        ]);
    }

    public function popular()
    {
        $result = SortBy::getPopularComposites();

        if (($composites = $result['composites'] ?? null) && $composites->isNotEmpty()) {
            $composites = $composites->map(function (\WP_Post $post) {
                $compositePost = WpModelRepository::instance()->getPost($post);
                $composite = new Composite(new CompositeAdapter($compositePost));
                return with(new CompositeTeaserTransformer)->transform($composite);
            })->values();
        }

        return new \WP_REST_Response(['data' => $composites->toArray()]);
    }
}
