<?php

namespace Bonnier\Willow\Base\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class TranslationManagerService
{
    private $client;
    private $serviceId;
    private $brand_id;
    
    public function __construct($host, $service, $brand_id)
    {
        $this->client = new Client([
            'base_uri' => rtrim($host, '/')
        ]);
        $this->serviceId = $service;
        $this->brand_id = $brand_id;
    }
    
    public function getTranslations()
    {
        $endpoint = sprintf('/api/v1/translations/service/%s/brand/%s', $this->serviceId, $this->brand_id);
        try {
            $response = $this->client->get($endpoint);
        } catch (ClientException $e) {
            return null;
        }
    
        $result = json_decode($response->getBody()->getContents());
        if (JSON_ERROR_NONE === json_last_error()) {
            return $result;
        }
        
        return null;
    }
}
