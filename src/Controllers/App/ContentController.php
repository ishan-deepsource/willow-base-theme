<?php

namespace Bonnier\Willow\Base\Controllers\App;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Helpers\Cache;
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

        register_rest_route('app/content', '/published', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, 'published']
        ]);

        register_rest_route('app/content', '/published/(?P<page>[0-9]+)', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, 'published'],
            'args' => ['page']
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

    public function published($request = null)
    {
        $currentPage = 1;
        if ($request && $request->get_param('page')) {
            $currentPage = $request->get_param('page');
        }

        return Cache::remember('published_page_' . $currentPage, 600, function() use ($currentPage) {
            $query_args = array(
                'post_type' => 'contenthub_composite',
                'post_status' => 'publish',
                'posts_per_page' => '100',
                'paged' => $currentPage,
                'orderby' => 'ID'
            );

            $response = [];
            $query = new \WP_Query($query_args);
            foreach ($query->posts as $post) {
                $response[] = get_permalink($post);
            }
            return new \WP_REST_Response(
                [
                    'count' => sizeof($query->posts),
                    'total' => intval($query->found_posts),
                    'page' => intval($currentPage),
                    'pages' => $query->max_num_pages,
                    'data' => $response
                ]
            );
        });
    }
}
