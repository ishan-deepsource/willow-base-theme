<?php

namespace Bonnier\Willow\Base\Commands;

use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\ContentHub\Editor\Models\WpComposite;
use Bonnier\Willow\Base\Repositories\CxenseSearchRepository;
use Bonnier\WP\Cxense\Services\CxenseApi;
use Bonnier\WP\Cxense\WpCxense;
use Bonnier\WP\Cxense\Exceptions\HttpException;
use WP_CLI;

class CxenseSync extends \WP_CLI_Command
{
    const CMD_NAMESPACE = 'cxense';
    const ALLOWED_ARGUMENTS = ['cleanup', 'sync'];

    const UPDATE_SLEEP_MSECS = 1000000; // 1 second
    const DELETE_SLEEP_MSECS = 1000000; // 1 second
    const GET_NEXT_PAGE_SLEEP_MSECS = 200000; // 0,2 second

    protected $onlyLocale;
    protected $skipLocale;
    protected $wait;

    protected $searchRepository;
    protected $inCxense;

    protected $deletedDidNotExist;
    protected $deletedDifferentUrl;

    protected $pushCount;
    protected $errorUrls;
    protected $skipCount;

    public static function register()
    {
        WP_CLI::add_command(static::CMD_NAMESPACE, __CLASS__);
    }

    public function run($params)
    {
        global $polylang, $locale;

        $this->inCxense = collect();
        $this->wait = false;

        // CleanUp stats
        $this->deletedDidNotExist = 0;
        $this->deletedDifferentUrl = 0;

        // Sync stats
        $this->pushCount = 0;
        $this->errorUrls = collect();
        $this->skipCount = 0;

        if (sizeof($params) == 0) {
            WP_CLI::line('Run one of the follwoing:');
            WP_CLI::line('');
            WP_CLI::line('wp ' . self::CMD_NAMESPACE . ' run cleanup');
            WP_CLI::line('wp ' . self::CMD_NAMESPACE . ' run sync');
            WP_CLI::line('wp ' . self::CMD_NAMESPACE . ' run sync cleanup');
            WP_CLI::line('wp ' . self::CMD_NAMESPACE . ' run cleanup sync');
            WP_CLI::line('');
            WP_CLI::line('It is also possible to add only:locale or skip:locale, e.g.');
            WP_CLI::line('');
            WP_CLI::line('wp ' . self::CMD_NAMESPACE . ' run sync only:da_DK');
            WP_CLI::line('wp ' . self::CMD_NAMESPACE . ' run cleanup skip:nb_NO');
            WP_CLI::line('');
            WP_CLI::line('Add \'wait\' to be prompted to press enter before each update/delete call to Cxense.');
            WP_CLI::line('When prompted you can write \'go\' to disable the prompting without restarting. E.g.');
            WP_CLI::line('');
            WP_CLI::line('wp ' . self::CMD_NAMESPACE . ' run cleanup wait');
            WP_CLI::line('');
            WP_CLI::line('\'cleanup\' will go through all content in Cxense.');
            WP_CLI::line('If an article has different urls in Cxense and WP, the article will be deleted in Cxense,');
            WP_CLI::line('and the WP article will (if it\'s published) be pushed to Cxense with correct url.');
            WP_CLI::line('');
            WP_CLI::line('\'sync\' will go through all published composites in WP and push them to Cxense.');
            WP_CLI::line('This will create the data in Cxense or update it if the url was already in Cxense.');
            WP_CLI::line('');
            WP_CLI::line('Remember to run sync before cleanup if you are moving a site to a new address,');
            WP_CLI::line('e.g. beta.illvid.dk to illvid.dk');
            WP_CLI::line('');
            WP_CLI::error('Argument is missing');
        }

        // Parse 'skip' and 'only' arguments
        $params = $this->parseArguments($params);

        // Check for illegal arguments
        if (sizeof(array_diff($params, self::ALLOWED_ARGUMENTS)) > 0) {
            WP_CLI::error('You have entered illegal arguments. Run the following command for info: wp ' .
                self::CMD_NAMESPACE . ' run');
        }

        collect($polylang->model->get_languages_list())->each(function (\PLL_Language $language) use (&$polylang,
            &$locale, $params) {

            $locale = $language->locale;

            if ($this->onlyLocale && $locale !== $this->onlyLocale) {
                return;
            }

            if ($this->skipLocale && $locale === $this->skipLocale) {
                return;
            }

            $polylang->curlang = $language;
            $this->searchRepository = new CxenseSearchRepository();

            WP_CLI::line('$language->locale: ' . $language->locale);
            WP_CLI::line('get_locale(): ' . get_locale());

            $this->runCommands($params);
        });

        WP_CLI::line('-- DONE --');
    }

    private function parseArguments($params)
    {
        $returnParams = [];
        foreach ($params as $param) {
            if (substr($param, 0, 5) === 'only:') {
                $this->onlyLocale = substr($param, 5, strlen($param) - 5);
                continue;
            }
            if (substr($param, 0, 5) === 'skip:') {
                $this->skipLocale = substr($param, 5, strlen($param) - 5);
                continue;
            }
            if ($param === 'wait') {
                $this->wait = true;
                continue;
            }
            $returnParams[] = $param;
        }
        return $returnParams;
    }

    private function runCommands($commands)
    {
        foreach ($commands as $command) {
            if ($command === 'cleanup') {
                WP_CLI::line('-- cleanup --');
                $this->cleanupCxense();
                continue;
            }
            if ($command === 'sync') {
                WP_CLI::line('-- sync --');
                $this->syncCompositesIntoCxense();
            }
        }
    }

    private function cleanupStat()
    {
        WP_CLI::line('Deleted - No such post:  ' . $this->deletedDidNotExist);
        WP_CLI::line('Deleted - Different URL: ' . $this->deletedDifferentUrl);
        WP_CLI::line('In Cxense: ' . $this->inCxense->count());
    }

    private function cleanupCxense()
    {
        // Iterate over Cxense urls and delete (in cxense) those with ids not in the composites
        $this->cxense_map_all(function ($obj) {
            print ". ";
            $postId = $obj->getField('recs-articleid');
            $cxenseUrl = $obj->getField('url');

            if (!$post=get_post($postId)) {
                // Delete in Cxense
                WP_CLI::line();
                WP_CLI::line('Delete in Cxense - No such post');
                $this->cxenseDeleteUrl($cxenseUrl);
                $this->deletedDidNotExist++;

                $this->cleanupStat();
                return;
            }

            $permalink = get_permalink($postId);

            if ($permalink !== $cxenseUrl) {
                // Delete in Cxense
                WP_CLI::line();
                WP_CLI::line('Delete in Cxense - Different URL');
                WP_CLI::line('postId: ' . $postId);
                WP_CLI::line('Cxense-url: ' . $cxenseUrl);
                WP_CLI::line('WP-url:     ' . $permalink);

                $this->cxenseDeleteUrl($cxenseUrl);
                $this->deletedDifferentUrl++;

                // Push the correct url to Cxense if the post is published
                if ($post->status === 'publish') {
                    WP_CLI::line('Pushing permalink to Cxense');
                    $this->cxensePush($permalink);
                }

                $this->cleanupStat();
            }

            $this->inCxense->push($postId);
        });
    }

    private function syncCompositesIntoCxense()
    {
        // Iterate over composite urls and add those to cxense that are not already there
        WpComposite::map_all(function (\WP_Post $post) {
            if (LanguageProvider::getPostLanguage($post->ID) !== LanguageProvider::getCurrentLanguage()) {
                return;
            }

            WP_CLI::line();
            WP_CLI::line('$post->ID: ' . $post->ID);
            WP_CLI::line('locale: ' . get_locale());

            $url = get_permalink($post->ID);
            WP_CLI::line('url: ' . $url);

            // Check if the id has been handled by cleanupCxense() earlier
            if ($this->inCxense->contains($post->ID)) {
                $this->skipCount++;
                return;
            }

            // Insert into Cxense
            $this->pushCount++;
            if (!$this->cxensePush($url)) {
                WP_CLI::error('Error pushing to Cxense', false);
                $this->errorUrls->push($url);
            } else {
                $this->inCxense->push($post->ID);
            }

            WP_CLI::line('skipCount: ' . $this->skipCount);
            WP_CLI::line('pushCount: ' . $this->pushCount);
            WP_CLI::line('in Cxense: ' . $this->inCxense->count());
            WP_CLI::line('errors: ' . $this->errorUrls->count());
            WP_CLI::line();
        });

        if ($this->errorUrls->count()>0) {
            WP_CLI::line('ErrorUrls:');
            var_dump($this->errorUrls);
        }
    }

    private function doCxenseSearch($query, $page = 1, $perPage = 100) {
        WP_CLI::line();
        WP_CLI::line('Page: ' . $page);
        return $this->searchRepository->getSearchResults(
            $query,
            $page,
            $perPage,
            [],
            [
                'type' => 'time',
                'order' => 'ascending'
            ]
        );
    }

    private function cxense_map_all($callback)
    {
        $query = '*';
        $page = 1;
        $perPage = 100;

        $searchResults = $this->doCxenseSearch($query, $page, $perPage);

        while (sizeof($searchResults->matches)) {
            collect($searchResults->matches)->each(function ($obj) use ($callback) {
                $callback($obj);
            });

            usleep(self::GET_NEXT_PAGE_SLEEP_MSECS);

            $page++;
            $searchResults = $this->doCxenseSearch($query, $page, $perPage);
        }
    }

    private function wait()
    {
        if ($this->wait) {
            $handle = fopen("php://stdin", "r");
            $line = rtrim(fgets($handle));
            if ($line === 'go') {
                $this->wait = false;
            }
        }
    }

    public function cxenseDeleteUrl($contentUrl)
    {
        usleep(self::DELETE_SLEEP_MSECS);
        $this->wait();
        return self::cxenseRequest(CxenseApi::CXENSE_PROFILE_DELETE, $contentUrl);
    }

    public function cxensePush($contentUrl)
    {
        usleep(self::UPDATE_SLEEP_MSECS);
        $this->wait();
        return self::cxenseRequest(CxenseApi::CXENSE_PROFILE_PUSH, $contentUrl);
    }

    public static function cxenseRequest($apiPath, $contentUrl)
    {
        if (WpCxense::instance()->settings->getEnabled()) {
            try {
                return CxenseApi::request($apiPath, ['url' => $contentUrl]);
            } catch (Exception $e) {
                if ($e instanceof HttpException) {
                    error_log('WP cXense: Failed calling cXense api: ' . $apiPath . ' response code: ' .
                        $e->getCode() . ' error: ' . $e->getMessage());
                }

                if ($e->getCode() == CxenseApi::EXCEPTION_USER_NOT_DEFINED) {
                    error_log('PHP Warning: To use CXense push you must define constants CXENSE_USER_NAME and CXENSE_API_KEY');
                } elseif ($e->getCode() == CxenseApi::EXCEPTION_UNAUTHORIZED) {
                    error_log('PHP Warning: Could not authorize with defined CXENSE_USER_NAME and CXENSE_API_KEY');
                }
            }
        }
    }
}