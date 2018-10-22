<?php

namespace Bonnier\Willow\Base\Commands;

use Bonnier\Willow\Base\Controllers\App\RouteController;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\Cache\Models\Post;
use Bonnier\WP\ContentHub\Editor\Models\WpComposite;
use Bonnier\WP\Cxense\Models\Post as CxensePost;
use Bonnier\WP\Redirect\Http\BonnierRedirect;
use Illuminate\Support\Collection;
use League\Csv\Exception;
use League\Csv\Reader;

class Cleanup extends \WP_CLI_Command
{
    const CMD_NAMESPACE = 'cleanup';

    protected $resolveCache;
    protected $output;
    protected $taxoFile;
    protected $skipFile;
    protected $doneFile;

    public static function register()
    {
        try {
            \WP_CLI::add_command(sprintf(
                '%s %s',
                CommandBootstrap::CORE_CMD_NAMESPACE,
                self::CMD_NAMESPACE
            ), __CLASS__);
        } catch (\Exception $exception) {
            \WP_CLI::warning($exception);
        }
    }

    /**
     * Delete a list of contenthub composites.
     * Supply a csv with url's of contenthub composites to be deleted.
     * The file should be located in the root of the project or an absolute path
     * needs to be provided.
     *
     * ## OPTIONS
     *
     * <csv>
     * : The input file with a list of url's to be deleted.
     *
     * [--delimiter=<delimiter>]
     * : The delimiter, will default to comma (,)
     * ---
     * default: ,
     * ---
     *
     * [--output=<output>]
     * : Whether or not to output skipped and taxonomy to csv files
     * --
     * ---
     * default: false
     * options:
     *   - true
     *   - false
     * ---
     *
     * ## EXAMPLES
     *     wp willow cleanup delete list.csv --delimiter=';' --output=true
     *
     * @param $args
     * @param $assocArgs
     *
     * @throws \WP_CLI\ExitException
     */
    public function delete($args, $assocArgs)
    {
        list($file) = $args;
        $delimiter = $assocArgs['delimiter'] ?: ',';
        $this->output = $assocArgs['output'] === 'true';
        if (!file_exists($file)) {
            $file = dirname(dirname(ABSPATH)) . $file;
            if (!file_exists($file)) {
                \WP_CLI::error('File not found');
            }
        }

        if ($this->output) {
            $this->taxoFile = sprintf('taxo-%s.csv', uniqid());
            \WP_CLI::line(sprintf('Taxonomy urls will be saved to %s', $this->taxoFile));
            $this->skipFile = sprintf('skip-%s.csv', uniqid());
            \WP_CLI::line(sprintf('Skipped urls will be saved to %s', $this->skipFile));
            $this->doneFile = sprintf('done-%s.csv', uniqid());
            \WP_CLI::line(sprintf('Done urls will be saved to %s', $this->doneFile));
        }

        $this->processCSV($file, $delimiter);

        \WP_CLI::success('Done!');
    }

    /**
     * Converts a CSV of 'from' and 'to' urls to a collection with associative arrays
     * [
     *    'from' => '/category/artice',
     *    'fromContent' => WP_Post{}
     *    'to' => '/category'
     *    'toContent' => WP_Term{}
     * ]
     *
     * @param string $file
     * @param string $delimiter
     *
     * @return Collection
     *
     * @throws \WP_CLI\ExitException
     */
    private function processCSV(string $file, string $delimiter): void
    {
        $csv = Reader::createFromPath($file, 'r');
        try {
            $csv->setDelimiter($delimiter);
            $csv->setHeaderOffset(0);
        } catch (Exception $exception) {
            \WP_CLI::error($exception->getMessage());
        }

        if ($this->output) {
            file_put_contents($this->taxoFile, '"from","to","Name","Slug","Taxonomy"' . PHP_EOL);
            file_put_contents($this->skipFile, '"from","to"' . PHP_EOL);
            file_put_contents($this->doneFile, '"WP ID","Name","Locale","from","to"' . PHP_EOL);
        }
        $this->resolveCache = [];
        $this->deactivateFilters();
        $progress = \WP_CLI\Utils\make_progress_bar(
            'Deleting content',
            count($csv)
        );
        foreach ($csv as $record) {
            $progress->tick();

            // Get the content that the from column matches
            // Either WP_Post or WP_Term (Pages, composites, categories or tags)
            $fromContent = $this->resolveContent($record['from'] ?? null);
            if (!$fromContent) {
                if ($this->output) {
                    file_put_contents(
                        $this->skipFile,
                        sprintf('"%s","%s"%s', $record['from'], $record['to'], PHP_EOL),
                        FILE_APPEND
                    );
                }
                continue;
            }
            if ($fromContent instanceof \WP_Post) {
                $fromTranslations = $this->getTranslations($fromContent);
                $fromTranslations->put('da', $fromContent);
                $toContent = $this->resolveContent($record['to'] ?? null);
                $toTranslations = $this->getTranslations($toContent);
                $toTranslations->put('da', $toContent);

                $fromTranslations->each(function ($content, $locale) use ($toTranslations, $record) {
                    $this->handleContent(
                        $content,
                        $toTranslations->get($locale),
                        $locale,
                        $record['to']
                    );
                });
            } elseif ($this->output) {
                file_put_contents($this->taxoFile, sprintf(
                    '"%s","%s",""%s","%s","%s"%s',
                    $record['from'],
                    $record['to'],
                    $fromContent->name,
                    $fromContent->slug,
                    $fromContent->taxonomy,
                    PHP_EOL
                ), FILE_APPEND);
            }
        }
        $progress->finish();
    }

    /**
     * Converting a relative url to its corresponding content.
     *
     * @param string $url
     * @return array|bool|null|\WP_Post|\WP_Term
     */
    private function resolveContent(string $url)
    {
        // Simple array cache to avoid re-resolving for instance the frontpage 5.000 times.
        if (($content = $this->resolveCache[$url] ?? false) && $content !== false) {
            return $content;
        }

        if (null === $url || '/' === $url) {
            $frontpage = get_post(get_option('page_on_front'));
            $this->resolveCache[$url] = $frontpage;
            return $frontpage;
        }

        if (preg_match('#/?tags/([^/]+)$#', $url, $match)) {
            $slug = $match[1];
            if ($tag = get_term_by('slug', $slug, 'post_tag')) {
                $this->resolveCache[$url] = $tag;
                return $tag;
            }
        }

        if (($category = get_category_by_path($url)) && $category instanceof \WP_Term) {
            $this->resolveCache[$url] = $category;
            return $category;
        }

        if (($page = get_page_by_path($url)) && $page instanceof \WP_Post) {
            $this->resolveCache[$url] = $page;
            return $page;
        }

        if ($composite = with(new RouteController)->findContenthubComposite($url, 'all')) {
            $this->resolveCache[$url] = $composite;
            return $composite;
        }

        $this->resolveCache[$url] = null;
        return null;
    }

    /**
     * Create a redirect and delete the "from"-content
     *
     * @param \WP_Post|\WP_Term|null $fromContent
     * @param \WP_Post|\WP_Term|null $toContent
     * @param string $locale
     */
    private function handleContent($fromContent, $toContent, $locale, $daTo)
    {
        $createRedirect = true;
        // If we cannot find a url, or the url is the frontpage
        // skip creating a redirect
        if ((!$fromUrl = $this->getUrl($fromContent)) || $fromUrl === '/' || $this->isDraft($fromContent)) {
            $createRedirect = false;
        }
        $toUrl = null;
        // If we cannot find a destination URL, we'll redirect to the frontpage
        if ($locale === 'da') {
            $toUrl = $daTo;
        } elseif ($createRedirect && !$toUrl = $this->getDestinationUrl($toContent, $fromContent, $fromUrl)) {
            $toUrl = '/';
        }

        // If from and to are the same
        // skip creating a redirect
        if ($createRedirect && $fromUrl === $toUrl) {
            $createRedirect = false;
        }

        if ($createRedirect) {
            // Create a redirect through the BonnierRedirect plugin
            BonnierRedirect::handleRedirect(
                $fromUrl,
                $toUrl,
                $locale,
                'cleanup-delete-script',
                $fromContent->ID ?? $fromContent->term_id ?? 0,
                301,
                true
            );
            if ($this->output) {
                file_put_contents($this->doneFile, sprintf(
                    '"%s","%s","%s","%s","%s"%s',
                    $fromContent->ID ?? 0,
                    $fromContent->post_title ?? '',
                    $locale,
                    $fromUrl,
                    $toUrl,
                    PHP_EOL
                ), FILE_APPEND);
            }
        } elseif ($this->output) {
            file_put_contents($this->doneFile, sprintf(
                '"%s","%s","%s","%s","%s"%s',
                $fromContent->ID ?? 0,
                $fromContent->post_title ?? '',
                $locale,
                '',
                '',
                PHP_EOL
            ), FILE_APPEND);
        }
        $this->deleteContent($fromContent);
    }

    /**
     * Convert the content to a url-path
     *
     * @param \WP_Post|\WP_Term|null $content
     * @return string|null
     */
    private function getUrl($content)
    {
        if ($content instanceof \WP_Post) {
            return parse_url(get_permalink($content->ID), PHP_URL_PATH);
        } elseif ($content instanceof \WP_Term && $content->taxonomy === 'category') {
            return parse_url(get_category_link($content->term_id), PHP_URL_PATH);
        } elseif ($content instanceof \WP_Term) {
            return parse_url(get_term_link($content->term_id), PHP_URL_PATH);
        }

        return null;
    }

    /**
     * Get the destination URL of the content
     * Added fallbacks if the content doesn't resolve in a path.
     *
     * @param \WP_Post|\WP_Term|null $content
     * @param \WP_Post|\WP_Term|null $fromContent
     * @param string $fallbackUrl
     * @return string|null
     */
    private function getDestinationUrl($content, $fromContent, $fallbackUrl)
    {
        // If the content has a URL, let's stick with that
        if ($url = $this->getUrl($content)) {
            return $url;
        }

        // If the fromContent has a parent category
        // let's redirect to that.
        if ($url = $this->getParentCategory($fromContent)) {
            return $url;
        }

        // If we have a fallback url, that's based on the "from"-url,
        // let's redirect to it's parent slug.
        if (preg_match('#^(.*)/.*$#', $fallbackUrl, $matches)) {
            // Get everything before the last dash.
            // Given our URL structure, the parent category should be
            // on the URL, that excludes the slug of the post, which should
            // be after the last dash.
            return parse_url($matches[1], PHP_URL_PATH);
        }

        return null;
    }

    /**
     * Get the path of the parent category of a
     * Page, post or category.
     * Default to frontpage if no matches are found
     *
     * @param \WP_Post|\WP_Term|null $content
     * @return string
     */
    private function getParentCategory($content)
    {
        if ($content instanceof \WP_Post) {
            if ($categories = wp_get_post_categories($content->ID)) {
                list($categoryId) = $categories;
                if ($categoryUrl = get_category_link($categoryId)) {
                    return parse_url($categoryUrl, PHP_URL_PATH);
                }
            }
        } elseif ($content instanceof \WP_Term && $content->taxonomy === 'category' && $parent = $content->parent) {
            if ($categoryUrl = get_category_link($parent)) {
                return parse_url($categoryUrl, PHP_URL_PATH);
            }
        }

        return '/';
    }

    /**
     * Delete the content
     *
     * @param \WP_Post $content
     */
    private function deleteContent(\WP_Post $content)
    {
        wp_trash_post($content->ID);
    }

    /**
     * Get all translations for the content.
     *
     * @param \WP_Post|\WP_Term|null $content
     * @return Collection
     */
    private function getTranslations($content): Collection
    {
        if (!$content) {
            return new Collection();
        }
        if ($content instanceof \WP_Post) {
            return collect(LanguageProvider::getPostTranslations($content->ID))->mapWithKeys(function ($id, $lang) {
                return [$lang => get_post($id)];
            });
        } elseif ($content instanceof \WP_Term) {
            return collect(LanguageProvider::getTermTranslations($content->term_id))
                ->mapWithKeys(function ($id, $lang) {
                    return [$lang => get_term($id)];
                });
        }

        return new Collection();
    }

    /**
     * Check whether the content is drafted or not
     *
     * @param \WP_Post|\WP_Term|null $content
     *
     * @return bool
     */
    private function isDraft($content)
    {
        if ($content instanceof \WP_Post) {
            return in_array($content->post_status, [
                'draft',
                'private',
                'trash',
                'auto-draft'
            ]);
        }

        return false;
    }

    private function deactivateFilters()
    {
        remove_action('wp_trash_post', [Post::class, 'removePost'], 10);
        remove_action('publish_to_draft', [Post::class, 'removePost'], 10);
        remove_action(WpComposite::SLUG_CHANGE_HOOK, [Post::class, 'urlChanged'], 10);
        remove_action('publish_to_publish', [Post::class, 'updatePost'], 10);
        remove_action('draft_to_publish', [Post::class, 'publishPost'], 10);
        remove_action('untrashed_post', [Post::class, 'publishPost'], 10);
        remove_action('transition_post_status', [CxensePost::class, 'post_status_changed'], 10);
    }
}
