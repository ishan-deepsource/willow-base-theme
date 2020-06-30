<?php

namespace Bonnier\Willow\Base\Repositories\WhiteAlbum;

use Exception;

/**
 * Class PanelRepository
 */
class PanelRepository
{
    private const PANEL_RESOURCE = '/api/v2/panels/';

    protected $client;
    protected $failedCsvFile;


    /**
     * PanelRepository constructor.
     *
     * @param null $locale Override default locale
     *
     *
     * @param null $failedCsvFile
     * @throws Exception
     */
    public function __construct($locale = null, $failedCsvFile = null)
    {
        $this->failedCsvFile = $failedCsvFile;
        $endpoint = null;
        if ($locale) {
            $envKey = sprintf('WHITEALBUM_ENDPOINT_%s', strtoupper($locale));
            if (! $endpoint = env($envKey)) { // env returns null by default, which would be a falsey value
                throw new Exception(sprintf('%s has not been defined in your ENV file.', $envKey));
            }
        }
        $this->client = new \GuzzleHttp\Client(
            [
                'base_uri' => $endpoint ?: env('WHITEALBUM_ENDPOINT'),
            ]
        );
    }

    /**
     * @param      $whitealbumId
     * @return array|mixed|null|object
     */
    public function findById($whitealbumId)
    {
        return $this->get(
            static::PANEL_RESOURCE . $whitealbumId,
            [
                'auth' => [
                    env('WHITEALBUM_USER'),
                    env('WHITEALBUM_PASSWORD'),
                ]
            ]
        );
    }

    public function getAll()
    {
        return $this->get(static::PANEL_RESOURCE, []);
    }

    /**
     * @param      $callback
     */
    public function mapAll($callback)
    {
        $panels = collect($this->getAll());
        collect($panels)->each(
            function ($panel) use ($callback) {
                $callback($panel);
            }
        );
    }

    private function get($url, $options, $attempt = 1)
    {
        try {
            $response = @$this->client->get($url, $options);
        } catch (Exception $e) {
            \WP_CLI::warning(sprintf('unable to fetch %s. %s will retry', $url, $e->getMessage()), false);
            if ($attempt < 5) {
                sleep(1); // Delay for a second before retrying
                return $this->get($url, $options, $attempt +1);
            }
            \WP_CLI::warning(sprintf('Failed 5 times will skip: %s', $url));
            $this->writeFailedImport($url, $e);
            return null;
        }
        if ($response->getStatusCode() !== 200) {
            return null;
        }
        return json_decode($response->getBody()->getContents());
    }

    /**
     * @param            $url
     * @param \Exception $exception
     */
    private function writeFailedImport($url, Exception $exception)
    {
        if ($this->failedCsvFile) {
            $urlParts = explode('/', $url);
            file_put_contents(
                $this->failedCsvFile,
                sprintf(
                    '%s%s,%s,%s,%s',
                    PHP_EOL, // Newline
                    end($urlParts), // Id
                    $this->client->getConfig('base_uri'), // Site url
                    $url, // Requested url
                    $exception->getCode() // respone code
                ),
                FILE_APPEND
            );
        }
    }
}
