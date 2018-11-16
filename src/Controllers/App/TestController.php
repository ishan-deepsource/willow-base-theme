<?php

namespace Bonnier\Willow\Base\Controllers\App;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use WP_Post;
use WP_Query;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class TestController extends WP_REST_Controller
{
    public function register_routes()
    {
        register_rest_route('app', '/test/teasers', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'teasers']
        ]);
    }

    public function teasers(WP_REST_Request $request): WP_REST_Response
    {
        $query = new WP_Query([
            'post_type' => 'contenthub_composite',
            'orderby' => 'rand',
            'post_status' => 'publish',
            'posts_per_page' => $request->get_param('amount') ?? 1
        ]);

        $posts = collect($query->get_posts())->map(function (WP_Post $post) {
            $composite = WpModelRepository::instance()->getPost($post);
            return new Composite(new CompositeAdapter($composite));
        });

        $manager = new Manager();
        $resource = new Collection($posts, new CompositeTeaserTransformer());
        return new WP_REST_Response($manager->createData($resource)->toArray());
    }
}
