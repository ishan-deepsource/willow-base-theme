<?php

namespace Bonnier\Willow\Base\Controllers\App;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Helpers\SortBy;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;
use WP_REST_Request;
use WP_REST_Response;

class ContentController extends BaseController
{
    public function register_routes()
    {
        register_rest_route('app/content', '/popular', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, 'popular']
        ]);

        register_rest_route('app/content', '/download', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, 'download']
        ]);
    }

    /**
     * @return WP_REST_Response
     */
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

    /**
     * @param \WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function download(WP_REST_Request $request)
    {
        $key = $request->get_param('key');
        $expires = $request->get_param('expires');
        if (is_numeric($expires)) {
            $expires = time() + $expires;
        }
        else {
            $expires = time() + HOUR_IN_SECONDS;
        }

        global $as3cf;
        $as3Client = $as3cf->get_s3client('eu-west-1');
        $signedUrl = $as3Client->get_object_url('files.bonnier.cloud', $key, $expires, []);

        return new \WP_REST_Response(['data' => [
            'signed_url' => $signedUrl,
        ]]);
    }
}
