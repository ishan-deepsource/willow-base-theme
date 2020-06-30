<?php

namespace Bonnier\Willow\Base\Repositories\WhiteAlbum;

use Exception;

/**
 * Class ImageRepository
 */
class ImageRepository
{
    public const IMAGE_RESOURCE = '/api/v2/images/';

    protected $client;
    protected $failedCsvFile;


    /**
     * PanelRepository constructor.
     * @param null $failedCsvFile
     */
    public function __construct($failedCsvFile = null)
    {
        $this->failedCsvFile = $failedCsvFile;
        $this->client = new \GuzzleHttp\Client(
            [
                'base_uri' => 'https://whitealbum.dk',
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
            static::IMAGE_RESOURCE . $whitealbumId,
            [
                'auth' => [
                    env('WHITEALBUM_USER'),
                    env('WHITEALBUM_PASSWORD'),
                ]
            ]
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
