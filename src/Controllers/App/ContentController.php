<?php

namespace Bonnier\Willow\Base\Controllers\App;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Helpers\Cache;
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
        } else {
            $expires = time() + HOUR_IN_SECONDS;
        }

        global $as3cf;
        $as3Client = $as3cf->get_s3client(env('AWS_S3_REGION'));
        $signedUrl = $as3Client->get_object_url(env('AWS_S3_BUCKET'), $key, $expires, []);

        return new \WP_REST_Response(['data' => [
            'signed_url' => $signedUrl,
        ]]);
    }

    /**
     * @return WP_REST_Response
     */
    public function popular(?\WP_REST_Request $request)
    {
        $categories = null;
        if (isset($request['categories'])) {
            $categories = explode(',', $request['categories']);
        }
        $result = SortBy::getPopularComposites($categories);

        if (($composites = $result['composites'] ?? null) && $composites->isNotEmpty()) {
            $composites = $composites->map(function (\WP_Post $post) {
                $compositePost = WpModelRepository::instance()->getPost($post);
                $composite = new Composite(new CompositeAdapter($compositePost));
                return with(new CompositeTeaserTransformer)->transform($composite);
            })->values();
        }

        return new \WP_REST_Response(['data' => $composites->toArray()]);
    }

    public function published(?\WP_REST_Request $request)
    {
        if (!$request) {
            return false;
        }

        $status = 'publish';
        $statusParam = $request->get_param('status');
        if (in_array($statusParam, ['any', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'])) {
            $status = $statusParam;
        }

        $perPage = 500;
        $perPageParam = $request->get_param('per_page');
        if (is_numeric($perPageParam)) {
            $perPage = $perPageParam;
        }

        $currentPage = 1;
        $pageParam = $request->get_param('page');
        if (is_numeric($pageParam)) {
            $currentPage = $pageParam;
        }

        $postId = null;
        $postIdParam = $request->get_param('id');
        if (is_numeric($postIdParam)) {
            $postId = $postIdParam;
        }

        return Cache::remember('page_' . $status . '_' . $perPage . '_' . $currentPage,
            600,
            function () use ($status, $perPage, $currentPage, $postId) {
                $query_args = [
                    'post_type' => 'contenthub_composite',
                    'post_status' => $status,
                    'posts_per_page' => $perPage,
                    'paged' => $currentPage,
                    'orderby' => 'modified',
                    'order' => 'desc',
                ];

                if ($postId) {
                    $query_args['post__in'] = [$postId];
                }

                $data = [];
                $query = new \WP_Query($query_args);
                foreach ($query->posts as $post) {
                    $data[] = [
                        'id' => $post->ID,
                        'canonical_url' => get_field('canonical_url', $post->ID),
                        'exclude_platforms' => get_field('exclude_platforms', $post->ID),
                        'hide_from_sitemap' => get_field('sitemap', $post->ID),
                        'post_date' => $post->post_date,
                        'post_date_gmt' => $post->post_date_gmt,
                        'modified' => $post->post_modified,
                        'modified_gmt' => $post->post_modified_gmt,
                        'status' => $post->post_status,
                        'title' => $post->post_title,
                        'url' => parse_url(get_permalink($post), PHP_URL_PATH),
                    ];
                }

                return new \WP_REST_Response(
                    [
                        'count' => sizeof($query->posts),
                        'total' => intval($query->found_posts),
                        'page' => intval($currentPage),
                        'pages' => $query->max_num_pages,
                        'pll_language' => pll_current_language(),
                        'bloginfo_language' => get_bloginfo("language"),
                        'locale' => get_locale(),
                        'home_url' => rtrim(pll_home_url(), '/'),
                        'version' => '3',
                        'data' => $data
                    ]
                );
            });
    }
}