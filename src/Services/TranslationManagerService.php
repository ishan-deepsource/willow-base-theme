<?php

namespace Bonnier\Willow\Base\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class TranslationManagerService
{
    private const CACHE_GROUP = 'translation-manager';
    private const CACHE_KEY = 'translations';
    private const CACHE_EXPIRE = '3200';

    private $client;
    private $serviceId;
    private $brandId;

    public function __construct($host, $service, $brandId)
    {
        $this->client = new Client([
            'base_uri' => rtrim($host, '/')
        ]);
        $this->serviceId = $service;
        $this->brandId = $brandId;
    }

    public function getTranslations($locale = null)
    {
        $cacheKey = $this->getCacheKey();
        $result = get_transient($cacheKey);
        if (false === $result || empty($result['data']) || $this->shouldUpdateCache($result)) {
            if ($locale == null)
                $endpoint = sprintf('/api/v1/translations/service/%s/brand/%s', $this->serviceId, $this->brandId);
            else
                $endpoint = sprintf('/api/v1/translations/service/%s/brand/%s/locale/%s', $this->serviceId, $this->brandId, $locale);
            try {
                $response = $this->client->get($endpoint);
                $decodedResponse = $this->decodeResponse($response);
                $result = [
                    'data' => $decodedResponse,
                    'timestamp' => time()
                ];
                set_transient($cacheKey, $result);
            } catch (Exception $e) {
                if (!empty($result['data'])) { // Avoid downtime if the cache is already populated
                    return $result['data'];
                }
                throw new Exception(
                    sprintf(
                        'Failed fetching site translations by brand: %s and service: %s %s url: %s',
                        $this->brandId,
                        $this->serviceId,
                        PHP_EOL,
                        $endpoint
                    ),
                    0,
                    $e
                );
            }
        }
        return $result['data'];
    }

    private function shouldUpdateCache($cacheResponse)
    {
        return (time() - $cacheResponse['timestamp'] ?? time()) > self::CACHE_EXPIRE;
    }

    private function getCacheKey()
    {
        return sprintf(
            '%s:%s:%s:%s',
            self::CACHE_GROUP,
            self::CACHE_KEY,
            $this->brandId,
            $this->serviceId
        );
    }

    private function decodeResponse(Response $response)
    {
        $decodedResponse = json_decode($response->getBody()->getContents());
        if (JSON_ERROR_NONE !== json_last_error() || empty($decodedResponse)) {
            throw new Exception('test');
        }
        return $decodedResponse;
    }
}
