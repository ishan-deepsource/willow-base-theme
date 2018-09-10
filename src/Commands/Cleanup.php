<?php

namespace Bonnier\Willow\Base\Commands;

use Bonnier\Willow\Base\Controllers\App\RouteController;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\Redirect\Http\BonnierRedirect;
use Illuminate\Support\Collection;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use WP_CLI;
use WP_CLI_Command;
use WP_Post;

class Cleanup extends WP_CLI_Command
{
    const CMD_NAMESPACE = 'cleanup';

    public static function register()
    {
        WP_CLI::add_command(sprintf(
            '%s %s',
            CommandBootstrap::CORE_CMD_NAMESPACE,
            self::CMD_NAMESPACE
        ), __CLASS__);
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
     * ## EXAMPLES
     *     wp contenthub editor cleanup delete list.csv --delimiter=';'
     *
     * @param $args
     *
     * @throws WP_CLI\ExitException
     */
    public function delete($args, $assoc_args)
    {
        list($file) = $args;
        $delimiter = $assoc_args['delimiter'] ?: ',';
        if (!file_exists($file)) {
            $file = dirname(dirname(ABSPATH)) . $file;
            if (!file_exists($file)) {
                WP_CLI::error('File not found');
            }
        }

        $urls = $this->processCSV($file, $delimiter);

        $articles = $this->processURLs($urls);

        $this->handleArticles($articles);

        WP_CLI::success('Done!');
    }

    private function processCSV(string $file, string $delimiter): Collection
    {
        $urls = new Collection();

        $csv = Reader::createFromPath($file, 'r');
        try {
            $csv->setDelimiter($delimiter);
            $csv->setHeaderOffset(0);
        } catch (Exception $exception) {
            WP_CLI::error($exception->getMessage());
        }

        foreach ($csv as $line => $record) {
            $fromUrl = $record['from'];
            $toUrl = $record['to'];
            if (!filter_var($fromUrl, FILTER_VALIDATE_URL)) {
                WP_CLI::error('Invalid input on line ' . ($line + 1));
            }
            $urls->put($fromUrl, $toUrl);
        }

        WP_CLI::line(sprintf('Found %s urls to be deleted%s', $urls->count(), PHP_EOL));

        return $urls;
    }

    private function processURLs(Collection $urls): Collection
    {
        $hostLanguage = collect(LanguageProvider::getLanguageList())->mapWithKeys(function ($language) {
            if (($homeUrl = $language->home_url ?? null) && ($slug = $language->slug ?? null)) {
                return [parse_url($homeUrl, PHP_URL_HOST) => $slug];
            }
            return null;
        })->reject(function ($language) {
            return is_null($language);
        });
        return $urls->map(function (string $toUrl, $fromUrl) use ($hostLanguage) {
            $host = parse_url($fromUrl, PHP_URL_HOST);
            $path = parse_url($fromUrl, PHP_URL_PATH);
            return [
                'from' => $fromUrl,
                'to' => $toUrl,
                'slug' => $hostLanguage->get($host),
                'post' => with(new RouteController)->findContenthubComposite($path, 'all'),
            ];
        });
    }

    private function handleArticles(Collection $articles)
    {
        $articles->each(function (array $article) {
            WP_CLI::line(sprintf('Deleting "%s"...', $article['from']));
            if ($post = $article['post']) {
                wp_delete_post($post->ID);
                WP_CLI::line(sprintf('Deleted post with id %s', $post->ID));
            } else {
                WP_CLI::line('No Composite to delete');
            }
            $this->handleRedirect($article);
            WP_CLI::line('Deletion done!' . PHP_EOL);
        });
    }

    private function handleRedirect(array $article)
    {
        $toUrl = null;
        if (filter_var($article['to'], FILTER_VALIDATE_URL)) {
            WP_CLI::line(sprintf('Creating redirect from "%s" to "%s"...', $article['from'], $article['to']));
            $toUrl = $article['to'];
        } else {
            WP_CLI::line('No destination URL.');
            WP_CLI::line('Creating redirect to parent category...');
            $toUrl = $this->getParentCategory($article);
            WP_CLI::line(sprintf('Creating redirect from "%s" to "%s"...', $article['from'], $toUrl));
        }

        if ($toUrl) {
            if (parse_url($article['from'], PHP_URL_PATH) == (parse_url($toUrl, PHP_URL_PATH) ?: '/')) {
                WP_CLI::warning('Cannot create redirect when "from" and "to" are identical!');
                return;
            }
            if (BonnierRedirect::handleRedirect(
                parse_url($article['from'], PHP_URL_PATH),
                parse_url($toUrl, PHP_URL_PATH) ?: '/',
                $article['slug'],
                'cleanup-delete-script',
                $article['post']->ID ?? 0
            ) === true) {
                WP_CLI::line('Redirect created');
            } else {
                WP_CLI::warning('Failed creating redirect');
            }
        } else {
            WP_CLI::warning('Unable to create redirect');
        }
    }

    private function getParentCategory(array $article): string
    {
        if (($post = $article['post']) && $post instanceof WP_Post) {
            list($categoryId) = wp_get_post_categories($post->ID);
            if ($categoryUrl = get_category_link($categoryId)) {
                return $categoryUrl;
            }
        }

        if (preg_match('#^(.*)/.*$#', $article['from'], $matches)) {
            // Get everything before the last dash.
            // Given our URL structure, the parent category should be
            // on the URL, that excludes the slug of the post, which should
            // be after the last dash.
            return $matches[1];
        }

        return '/';
    }
}
