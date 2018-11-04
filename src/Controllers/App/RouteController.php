<?php

namespace Bonnier\Willow\Base\Controllers\App;

use Bonnier\Willow\Base\Repositories\WhiteAlbum\RedirectRepository;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\ContentHub\Editor\Models\WpComposite;
use Bonnier\WP\Redirect\Http\BonnierRedirect;
use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\PageAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Categories\CategoryAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Tags\TagAdapter;
use Bonnier\Willow\Base\Helpers\Cache;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Models\Base\Pages\Page;
use Bonnier\Willow\Base\Models\Base\Terms\Category;
use Bonnier\Willow\Base\Models\Base\Terms\Tag;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTransformer;
use Bonnier\Willow\Base\Transformers\Api\Pages\PageTransformer;
use Bonnier\Willow\Base\Transformers\Api\Terms\Category\CategoryTransformer;
use Bonnier\Willow\Base\Transformers\Api\Terms\Tag\TagTransformer;
use Bonnier\Willow\Base\Transformers\NullTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use WP_Post;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_Term;

class RouteController extends BaseController
{
    const STATUS_PUBLISHED = 'publish';
    const STATUS_SCHEDULED = 'future';
    const STATUS_DRAFT = 'draft';

    /* @var \Bonnier\Willow\Base\Repositories\WhiteAlbum\RedirectRepository */
    protected $waRedirectRepository;

    public function register_routes()
    {
        $this->waRedirectRepository = new RedirectRepository(
            parse_url(LanguageProvider::getHomeUrl(), PHP_URL_HOST),
            LanguageProvider::getCurrentLanguage()
        );
        register_rest_route('app', '/resolve', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, 'resolve']
        ]);
    }

    public function resolve(WP_REST_Request $request)
    {
        $locale = $request->get_param('lang');
        $path = $request->get_param('path');
        $content = Cache::remember(
            'path-resolve:' . $path . '-' . $locale,
            2 * 3600,
            function () use ($path, $locale) {
                return $this->resolveContent($path, $locale);
            }
        );

        $resource = null;

        if ($content instanceof WP_Post && $content->post_type === 'contenthub_composite') {
            $composite = new Composite(new CompositeAdapter($content));
            $resource = new Item($composite, new CompositeTransformer());
            $resource->setMeta(['type' => 'composite']);
        } elseif ($content instanceof WP_Post && $content->post_type === 'page') {
            $page = new Page(new PageAdapter($content));
            $resource = new Item($page, new PageTransformer());
            $resource->setMeta(['type' => 'page']);
        } elseif ($content instanceof WP_Term && $content->taxonomy === 'category') {
            $category = new Category(new CategoryAdapter($content));
            $resource = new Item($category, new CategoryTransformer());
            $resource->setMeta(['type' => $content->parent ? 'subcategory' : 'category']);
        } elseif ($content instanceof WP_Term && $content->taxonomy === 'post_tag') {
            $tag = new Tag(new TagAdapter($content));
            $resource = new Item($tag, new TagTransformer());
            $resource->setMeta(['type' => 'tag']);
        } elseif ($content === 'search') {
            $resource = new Item(null, new NullTransformer());
            $resource->setMeta(['type' => 'search']);
        } elseif (isset($content['type']) && $content['type'] === 'redirect') {
            $resource = new Item(null, new NullTransformer());
            $resource->setMeta([
                'type' => 'redirect',
                'location' => $content['redirect']->to,
                'status' => $content['redirect']->code
            ]);
        }

        if ($resource) {
            $manager = new Manager();
            $data = $manager->createData($resource)->toArray();
            return new WP_REST_Response($data);
        } else {
            return new WP_REST_Response([
                'status' => 404,
            ], 404);
        }
    }

    private function resolveContent($path, $locale)
    {
        $query = parse_url(urldecode($path), PHP_URL_QUERY);
        parse_str($query, $queryParams);
        $path = parse_url(urldecode($path), PHP_URL_PATH);

        // Route resolving for previewing article drafts
        if (($queryParams['preview'] ?? false) && ($queryParams['post_type'] ?? false) && ($queryParams['p'] ?? false)) {
            $posts =  get_posts([
                'post_type' => $queryParams['post_type'],
                'include' => $queryParams['p'], // Wordpress way of saying give me the content that match id
                'post_status' => self::STATUS_DRAFT,
            ]);
            if (count($posts)) {
                return $posts[0];
            }
        }

        // Route resolving for previewing scheduled articles
        if (($queryParams['preview'] ?? false) && $path) {
            if ($scheduled = $this->resolvePath($path, self::STATUS_SCHEDULED)) {
                return $scheduled;
            }

            // If a scheduled article wasn't found, we'll look for a published one.
            return $this->resolvePath($path, self::STATUS_PUBLISHED);
        }

        // Route resolving for search page or frontpage
        if (null === $path || '/' === $path) {
            if ($query && substr($query, 0, 2) === 's=') {
                return 'search';
            }
            $frontpageID = get_option('page_on_front');
            if ($translations = LanguageProvider::getPostTranslations($frontpageID)) {
                return get_post($translations[$locale] ?? null);
            }
            return get_post($frontpageID);
        }

        // Route resolving for tag pages
        if (preg_match('#/?tags/([^/]+)$#', $path, $match)) {
            $slug = $match[1];
            if ($tag = get_term_by('slug', $slug, 'post_tag')) {
                return $tag;
            }
        }

        // Route resolving for all other content
        return $this->resolvePath($path);
    }

    private function resolvePath($path, string $status = self::STATUS_PUBLISHED)
    {
        if ($page = $this->findPage($path, $status)) {
            return $page;
        }

        if (($category = get_category_by_path($path)) && $category instanceof WP_Term) {
            return $category;
        }

        if (($composite = $this->findContenthubComposite($path, $status))) {
            return $composite;
        }

        if ($redirect = $this->findRedirect($path)) {
            return [
                'type' => 'redirect',
                'redirect' => $redirect
            ];
        }

        return null;
    }

    private function findPage(string $path, string $status = self::STATUS_PUBLISHED): ?WP_Post
    {
        $page = get_page_by_path($path);
        if (!$page || !$page instanceof WP_Post || $page->post_status !== $status) {
            return null;
        }

        $parent_id = $page->post_parent;

        while ($parent_id) {
            $parent = get_post($parent_id);
            if ($parent) {
                if ($parent->post_status === $status) {
                    $parent_id = $parent->post_parent;
                } else {
                    return null;
                }
            } else {
                $parent_id = null;
            }
        }

        return $page;
    }

    public function findContenthubComposite(string $path, string $status = self::STATUS_PUBLISHED): ?WP_Post
    {
        $parts = preg_split('#/#', $path, -1, PREG_SPLIT_NO_EMPTY);

        $content = null;

        foreach ($parts as $part) {
            if ($category = get_category_by_slug($part)) {
                if ($content && !$content instanceof WP_Term) {
                    return null;
                } elseif ($content && $category->parent !== $content->term_id) {
                    return null;
                } elseif (!$content && 0 !== $category->parent) {
                    return null;
                } else {
                    $content = $category;
                }
            } elseif ($composite = get_page_by_path($part, OBJECT, WpComposite::POST_TYPE)) {
                $cat = get_field('category', $composite->ID);
                if ($composite->post_status !== $status && $status !== 'all') {
                    return null;
                } elseif ($content && !$content instanceof WP_Term) {
                    // The parent element of the slug is not a WP_Term
                    // Composites should be attached to WP_Term (categories)
                    return null;
                } elseif ($content && $cat->term_id !== $content->term_id) {
                    // The parent element of the slug is not the composites category
                    return null;
                } elseif (!$content && $cat) {
                    // The composite is attached to a category, but is accessed directly
                    return null;
                } else {
                    return $composite;
                }
            } else {
                return null;
            }
        }

        return null;
    }

    private function findRedirect($path)
    {
        if (!class_exists(BonnierRedirect::class)) {
            return null;
        }
        try {
            if ($bonnierRedirect = BonnierRedirect::recursiveRedirectFinder($path)) {
                return $bonnierRedirect;
            }
        } catch (\Exception $exception) {
            // Empty because we just need to go to the next line.
        }
        if (env('RESOLVE_WA_REDIRECTS') && env('WP_ENV') !== 'testing' && $redirect = $this->findWaRedirect($path)) {
            BonnierRedirect::createRedirect(
                $path,
                $redirect->to,
                LanguageProvider::getCurrentLanguage(),
                'wa-route-resolve',
                null
            );
            return $redirect;
        }

        return null;
    }

    private function findWaRedirect($path)
    {
        $redirect = $this->waRedirectRepository->resolve($path);
        if ($redirect) {
            return (object)[
                'to' => $redirect->to,
                'code' => 301
            ];
        }
        return null;
    }
}
