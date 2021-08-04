<?php

namespace Bonnier\Willow\Base\Controllers\App;

use Bonnier\Willow\Base\Adapters\Wp\Root\AuthorAdapter;
use Bonnier\Willow\Base\Models\Base\Root\Author;
use Bonnier\Willow\Base\Models\WpComposite;
use Bonnier\Willow\Base\Repositories\NotFoundRepository;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Repositories\WhiteAlbum\RedirectRepository;
use Bonnier\Willow\Base\Transformers\Api\Root\AuthorTransformer;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Pages\PageAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Categories\CategoryAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Tags\TagAdapter;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Models\Base\Pages\Page;
use Bonnier\Willow\Base\Models\Base\Terms\Category;
use Bonnier\Willow\Base\Models\Base\Terms\Tag;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTransformer;
use Bonnier\Willow\Base\Transformers\Api\Pages\PageTransformer;
use Bonnier\Willow\Base\Transformers\Api\Terms\Category\CategoryTransformer;
use Bonnier\Willow\Base\Transformers\Api\Terms\Tag\TagTransformer;
use Bonnier\Willow\Base\Transformers\NullTransformer;
use Bonnier\WP\Redirect\Helpers\LocaleHelper;
use Bonnier\WP\Redirect\Models\Redirect;
use Bonnier\WP\Redirect\WpBonnierRedirect;
use Exception;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceAbstract;
use Symfony\Component\HttpFoundation\Response;
use WP_Post;
use WP_Query;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_Term;

class RouteController extends BaseController
{
    private const STATUS_PUBLISHED = 'publish';
    private const STATUS_SCHEDULED = 'future';
    private const STATUS_DRAFT = 'draft';

    /* @var RedirectRepository */
    protected $waRedirectRepository;

    public function register_routes()
    {
        $this->waRedirectRepository = new RedirectRepository(
            parse_url(LanguageProvider::getHomeUrl(), PHP_URL_HOST),
            LanguageProvider::getCurrentLanguage()
        );
        register_rest_route('app', '/resolve', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'resolve']
        ]);
    }

    public function resolve(WP_REST_Request $request)
    {
        $locale = $request->get_param('lang');
        $path = $request->get_param('path');
        $content = $this->resolveContent($path, $locale);
        if (is_null($content) && $redirect = $this->findRedirect($path)) {
            $content = [
                'type' => 'redirect',
                'redirect' => $redirect
            ];
        }

        $resource = null;

        if ($content instanceof WP_Post && $content->post_type === 'contenthub_composite') {
            $composite = new Composite(new CompositeAdapter($content));
            $resource = new Item($composite, new CompositeTransformer());
            $resource->setMeta([
                'type' => 'composite',
                'status' => 200
            ]);
        } elseif ($content instanceof WP_Post && $content->post_type === 'page') {
            $page = new Page(new PageAdapter($content));
            $resource = new Item($page, new PageTransformer());
            $meta = ['type' => 'page'];
            if ($page->getTemplate() === '404-page') {
                $meta['type'] = '404';
                $meta['status'] = 404;
            }
            $resource->setMeta($meta);
        } elseif ($content instanceof WP_Term && $content->taxonomy === 'category') {
            $category = new Category(new CategoryAdapter($content));
            $resource = new Item($category, new CategoryTransformer());
            $resource->setMeta(['type' => $content->parent ? 'subcategory' : 'category']);
        } elseif ($content instanceof WP_Term && $content->taxonomy === 'post_tag') {
            $category = get_term_by('slug', $content->slug, 'category');

            // If tag has the same slug as category, redirect to category instead
            if ($category && pll_get_term_language($category->term_id) === $locale) {
                $resource = new Item(null, new NullTransformer());
                $resource->setMeta([
                    'type' => 'redirect',
                    'location' => parse_url(get_category_link($category), PHP_URL_PATH),
                    'status' => Response::HTTP_MOVED_PERMANENTLY
                ]);
            } else {
                $tag = new Tag(new TagAdapter($content));
                $resource = new Item($tag, new TagTransformer());
                $resource->setMeta(['type' => 'tag']);
            }
        } elseif ($content instanceof \WP_User) {
            $author = new Author(new AuthorAdapter($content));
            $resource = new Item($author, new AuthorTransformer());
            $resource->setMeta(['type' => 'author']);
        } elseif ($content === 'search') {
            $resource = new Item(null, new NullTransformer());
            $resource->setMeta(['type' => 'search']);
        } elseif (isset($content['type']) && $content['type'] === 'redirect') {
            $resource = new Item(null, new NullTransformer());
            $resource->setMeta([
                'type' => 'redirect',
                'location' => $content['redirect']->getTo(),
                'status' => $content['redirect']->getCode()
            ]);
        }

        if ($resource) {
            if ($resource->getMeta()['type'] !== 'redirect' && $redirectPath = $this->shouldPathRedirect($path)) {
                $resource = new Item(null, new NullTransformer());
                $resource->setMeta([
                    'type' => 'redirect',
                    'location' => $redirectPath,
                    'status' => 301,
                ]);
            }
            $manager = new Manager();
            $data = $manager->createData($resource)->toArray();
            if (array_get($data, 'data.template') === '404-page') {
                return $this->return404Page($path, $locale, $data);
            }
            return new WP_REST_Response($data);
        } else {
            return $this->return404Page($path, $locale);
        }
    }

    private function return404Page(string $path, string $locale, ?array $data = [])
    {
        NotFoundRepository::instance()->register($path, $locale);

        if (empty($data)) {
            $post = collect(
                get_posts([
                    'post_type' => 'page',
                    'meta_key' => '_wp_page_template',
                    'meta_value' => '404-page',
                ])
            )->first(function (WP_Post $post) {
                return LanguageProvider::getPostLanguage($post->ID) === LanguageProvider::getCurrentLanguage();
            });
            if ($post instanceof WP_Post) {
                $page = new Page(new PageAdapter($post));
                $resource = new Item($page, new PageTransformer());
            } else {
                $resource = new Item(null, new NullTransformer());
            }
            $resource->setMeta([
                'type' => '404',
                'status' => 404
            ]);
            $manager = new Manager();
            $data = $manager->createData($resource)->toArray();
        }
        return new WP_REST_Response($data, 404);
    }

    private function shouldPathRedirect($path)
    {
        $cleanPath = parse_url($path, PHP_URL_PATH);
        if ($cleanPath === '/') {
            return false;
        }
        $newPath = mb_strtolower(rtrim($cleanPath, '/'));

        // We need to URL decode the strings, because urlencoded characters
        // will be uppercase, and that will make the urls differ, even though
        // they are actually the same. For instance /?preview=true will be
        // converted to %3Fpreview%3Dtrue, and the encoded charachters will
        // then be lowercased, which will not match the actual path.
        if (urldecode($newPath) === urldecode($cleanPath)) {
            return false;
        }

        if ($query = parse_url($path, PHP_URL_QUERY)) {
            $newPath .= sprintf('?%s', $query);
        }

        return $newPath;
    }

    private function resolveContent($path, $locale)
    {
        if (substr($path, 0, 2) === '//') {
            return null;
        }
        $query = parse_url(urldecode($path), PHP_URL_QUERY);
        parse_str($query, $queryParams);
        $path = parse_url(urldecode($path), PHP_URL_PATH);

        // Route resolving for previewing article drafts
        if (
            ($queryParams['preview'] ?? false)
        ) {
            $posts = [];
            if (
                ($queryParams['post_type'] ?? false) &&
                ($queryParams['p'] ?? false)
            ) {
                $posts = get_posts([
                    'post_type' => $queryParams['post_type'],
                    'include' => $queryParams['p'], // Wordpress way of saying give me the content that match id
                    'post_status' => self::STATUS_DRAFT,
                ]);
            } else if ($queryParams['page_id'] ?? false) {
                $posts = get_posts([
                    'post_type' => 'page',
                    'include' => $queryParams['page_id'],
                    'post_status' => self::STATUS_DRAFT
                ]);
            }
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
                return WpModelRepository::instance()->getPost($translations[$locale] ?? null);
            }
            return WpModelRepository::instance()->getPost($frontpageID);
        }

        // Route resolving for tag pages
        if (preg_match('#/?tags/([^/]+)/?$#', $path, $match)) {
            $slug = $match[1];
            if ($tag = get_term_by('slug', $slug, 'post_tag')) {
                return $tag;
            }
        }

        if (preg_match('#/?author/([^/]+)/?$#', $path, $match)) {
            $slug = $match[1];
            if ($author = get_user_by('slug', $slug)) {
                return $author;
            }
        }

        // Route resolving for all other content
        return $this->resolvePath($path, self::STATUS_PUBLISHED, $locale);
    }

    private function resolvePath($path, string $status = self::STATUS_PUBLISHED, ?string $locale = null)
    {
        if ($page = $this->findPage($path, $locale, $status)) {
            return $page;
        }

        if (($category = get_category_by_path($path)) && $category instanceof WP_Term) {
            return $category;
        }

        if (($composite = $this->findContenthubComposite($path, $status, $locale))) {
            return collect(data_get($composite, 'exclude_platforms'))->contains('web') ? null : $composite;
        }

        return null;
    }

    private function findPage(string $path, ?string $locale = null, string $status = self::STATUS_PUBLISHED): ?WP_Post
    {
        $parts = preg_split('#/#', $path, -1, PREG_SPLIT_NO_EMPTY);
        $pageSlug = end($parts); // Get the composite slug from the last part
        $page = $this->getPost($pageSlug, 'page', $locale, $status);
        if (!$page || !$page instanceof WP_Post || $page->post_status !== $status) {
            return null;
        }

        $parent_id = $page->post_parent;

        while ($parent_id) {
            $parent = WpModelRepository::instance()->getPost($parent_id);
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

        if (parse_url(get_permalink($page), PHP_URL_PATH) !== $path) {
            // If the permalink path does not match the submitted path,
            // we are returning a wrong page - maybe a top level page instead of a child page
            return null;
        }

        return $page;
    }

    public function findContenthubComposite(
        string $path,
        string $status = self::STATUS_PUBLISHED,
        ?string $locale = null
    ): ?WP_Post {
        $parts = preg_split('#/#', $path, -1, PREG_SPLIT_NO_EMPTY);
        $compositeSlug = end($parts); // Get the composite slug from the last part
        array_pop($parts); // Remove composite slug
        $categorySlug = implode('/', $parts); // Glue parts to form category slug

        if (($category = get_category_by_path($categorySlug)) && $category instanceof WP_Term) {
            if (
                ($composite = $this->getPost($compositeSlug, WpComposite::POST_TYPE, $locale, $status)) &&
                $composite instanceof WP_Post
            ) {
                if (in_array($category->term_id, $composite->post_category)) {
                    return $composite;
                }
            }
        }
        return null;
    }

    private function getPost(
        string $slug,
        string $postType,
        ?string $locale = null,
        $status = self::STATUS_PUBLISHED
    ): ?WP_Post {
        $query = new WP_Query([
            'name' => $slug,
            'post_type' => $postType,
            'post_status' => $status,
            'lang' => $locale ?? LanguageProvider::getCurrentLanguage(),
        ]);
        return $query->posts[0] ?? null;
    }

    private function findRedirect($path): ?Redirect
    {
        if (!class_exists(WpBonnierRedirect::class)) {
            return null;
        }
        try {
            if ($bonnierRedirect = WpBonnierRedirect::instance()->getRedirectRepository()->findRedirectByPath($path)) {
                return $bonnierRedirect;
            }
        } catch (Exception $exception) {
            // Empty because we just need to go to the next line.
        }
        if (env('RESOLVE_WA_REDIRECTS') && env('WP_ENV') !== 'testing' && $waRedirect = $this->findWaRedirect($path)) {
            $redirect = new Redirect();
            $redirect->setFrom($path)
                ->setTo($waRedirect->to)
                ->setLocale(LocaleHelper::getLanguage())
                ->setType('wa-route-resolve')
                ->setCode(301);
            try {
                return WpBonnierRedirect::instance()->getRedirectRepository()->save($redirect);
            } catch (Exception $exception) {
                return null;
            }
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
