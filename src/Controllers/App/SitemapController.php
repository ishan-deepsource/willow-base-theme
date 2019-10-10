<?php

namespace Bonnier\Willow\Base\Controllers\App;

use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\ContentHub\Editor\Models\WpComposite;
use Bonnier\Willow\Base\Adapters\Wp\Root\SitemapCategoryAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\SitemapCollectionAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\SitemapPostAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\SitemapTagAdapter;
use Bonnier\Willow\Base\Helpers\Cache;
use Bonnier\Willow\Base\Models\Base\Root\SitemapCollection;
use Bonnier\Willow\Base\Models\Base\Root\SitemapItem;
use Bonnier\Willow\Base\Transformers\Api\Root\SitemapTransformer;
use DateTime;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class SitemapController extends WP_REST_Controller
{
    const POST_TYPES = [
        'page',
        WpComposite::POST_TYPE,
    ];
    const TAXONOMIES = [
        'post_tag',
        'category'
    ];
    const ADAPTER_MAPPING = [
        'page' => SitemapPostAdapter::class,
        WpComposite::POST_TYPE => SitemapPostAdapter::class,
        'post_tag' => SitemapTagAdapter::class,
        'category' => SitemapCategoryAdapter::class,
    ];
    const PER_PAGE = 100;
    public function register_routes()
    {
        register_rest_route(
            'app', '/sitemaps', [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'sitemaps'],
            ]
        );
        register_rest_route(
            'app', '/sitemaps/(?P<type>[a-zA-Z0-9-_]+)', [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'content']
            ]
        );
    }
    public function sitemaps(WP_REST_Request $request)
    {
        $perPage = self::PER_PAGE;
        if ($queryPerPage = $request->get_param('per_page')) {
            $perPage = intval($queryPerPage);
        }
        $posts = collect(self::POST_TYPES)->map(
            function (string $postType) use ($perPage) {
                $lastModPost = get_posts(
                    [
                        'post_type' => $postType,
                        'numberposts' => 1,
                        'orderby' => 'modified',
                        'order' => 'DESC',
                        'post_status' => 'publish',
                        'lang' => LanguageProvider::getCurrentLanguage(),
                    ]
                );
                $lastMod = (new DateTime($lastModPost[0]->post_modified_gmt))->format('c');
                $pages = ceil($this->getTotalPostCount($postType) / $perPage);
                $urls = [];
                for ($i = 1; $i <= $pages; $i++) {
                    $urls[] = $postType . '-' . $i;
                }
                return [
                    'type' => $postType,
                    'lastmod' => $lastMod,
                    'pages' => $pages,
                    'urls' => $urls
                ];
            }
        );
        $terms = collect(self::TAXONOMIES)->map(
            function (string $term) use ($posts, $perPage) {
                $termsCount = wp_count_terms($term, [
                    'exclude' => $this->getExcludedPostTagsIds($term),
                ]);
                $pages = ceil(intval($termsCount) / $perPage);
                $urls = [];
                for ($i = 1; $i <= $pages; $i++) {
                    $urls[] = $term . '-' . $i;
                }
                return [
                    'type' => $term,
                    'lastmod' => $posts->first(
                        function (array $post) {
                            return $post['type'] === WpComposite::POST_TYPE;
                        }
                    )['lastmod'],
                    'pages' => $pages,
                    'urls' => $urls
                ];
            }
        );
        return new WP_REST_Response(['data' => $posts->merge($terms)]);
    }
    public function content(WP_REST_Request $request)
    {
        $perPage = self::PER_PAGE;
        if ($queryPerPage = $request->get_param('per_page')) {
            $perPage = intval($queryPerPage);
        }
        $type = $request->get_param('type');
        $page = $request->get_param('page') ?? 1;
        if (!in_array($type, array_merge(self::POST_TYPES, self::TAXONOMIES))) {
            return new WP_REST_Response(
                [
                    'code' => 'no_sitemap_found',
                    'message' => 'No sitemap was found for this type',
                    'data' => [
                        'status' => 404
                    ]
                ],
                404
            );
        }
        $data = Cache::remember(
            sprintf('sitemap-%s-%s', $type, $page),
            4 * HOUR_IN_SECONDS,
            function () use ($type, $page, $perPage) {
                $offset = ($page - 1) * $perPage;
                if (in_array($type, self::POST_TYPES)) {
                    $args = [
                        'post_type' => $type,
                        'posts_per_page' => $perPage,
                        'offset' => $offset,
                        'post_status' => 'publish',
                        'lang' => LanguageProvider::getCurrentLanguage(),
                        'orderby' => ['post_modified' => 'DESC', 'ID' => 'DESC'],
                    ];
                    if ($type === 'page') {
                        $args['meta_query'] = [
                            'relation' => 'AND',
                            [
                                'relation' => 'OR',
                                [
                                    'key' => 'sitemap',
                                    'value' => '0',
                                    'compare' => '=',
                                ],
                                [
                                    'key' => 'sitemap',
                                    'compare' => 'NOT EXISTS',
                                ],
                            ],
                            [
                                'relation' => 'OR',
                                [
                                    'key' => '_wp_page_template',
                                    'value' => '404-page',
                                    'compare' => '!=',
                                ],
                                [
                                    'key' => '_wp_page_template',
                                    'compare' => 'NOT EXISTS',
                                ],
                            ],
                        ];
                    }
                    if ($type === WpComposite::POST_TYPE) {
                        $args['meta_query'] = [
                            'relation' => 'OR',
                            [
                                'key' => 'sitemap',
                                'value' => '0',
                                'compare' => '=',
                            ],
                            [
                                'key' => 'sitemap',
                                'compare' => 'NOT EXISTS',
                            ],
                        ];
                    }
                    $contents = get_posts($args);
                } else {
                    $contents = get_terms([
                        'taxonomy' => $type,
                        'number' => $perPage,
                        'offset' => $offset,
                        'exclude' => $this->getExcludedPostTagsIds($type),
                    ]);
                }
                $sitemapCollection = collect($contents)->map(
                    function ($content) use ($type) {
                        $class = collect(self::ADAPTER_MAPPING)->get($type);
                        return new SitemapItem(new $class($content));
                    }
                );
                $sitemap = new SitemapCollection(new SitemapCollectionAdapter($type, $sitemapCollection));
                $manager = new Manager();
                $resource = new Item($sitemap, new SitemapTransformer());
                return $manager->createData($resource)->toArray();
            }
        );
        return new WP_REST_Response($data);
    }
    /**
     * Get an array of post tags term ids,
     * that have less than five posts attached to it.
     *
     * @param string $taxonomy
     * @return array
     */
    private function getExcludedPostTagsIds(string $taxonomy)
    {
        if ($taxonomy === 'post_tag') {
            return collect(get_terms($taxonomy))->reject(function (\WP_Term $tag) {
                return $tag->count >= 5;
            })->pluck('term_id')->toArray();
        }
        return [];
    }
    private function getTotalPostCount($postType)
    {
        return with(new \WP_Query([
            'fields' => 'ids',
            'post_type' => $postType,
            'posts_per_page' => 1,
            'post_status' => 'publish',
            'lang' => LanguageProvider::getCurrentLanguage(),
        ]))->found_posts;
    }
}
