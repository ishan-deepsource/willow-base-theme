<?php

namespace Bonnier\Willow\Base\Repositories\SiteManager;

use Bonnier\Willow\Base\Repositories\Contracts\SiteManager\SiteContract;
use Bonnier\Willow\Base\Services\SiteManagerService;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

/**
 * Class SiteRepository
 */
class SiteRepository implements SiteContract
{
    public static function get_all()
    {
        try {
            $response = SiteManagerService::getInstance()->get('/api/v1/sites');
        } catch (ClientException $e) {
            return [];
        }
        return $response->getStatusCode() === 200 ?
            json_decode($response->getBody()->getContents())->data :
            [];
    }

    public static function find_by_id($id)
    {
        if (is_null($id)) {
            return null;
        }
        try {
            $response = SiteManagerService::getInstance()->get('/api/v1/sites/'.$id);
        } catch (ClientException $e) {
            return null;
        }
        return $response->getStatusCode() === 200 ?
            json_decode($response->getBody()->getContents()) :
            null;
    }

    public static function find_by_domain($domain)
    {
        try {
            $response = SiteManagerService::getInstance()->get(sprintf('/api/v1/sites/domain/%s', $domain));
            if ($response) {
                $site = json_decode($response->getBody()->getContents());
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $site;
                }
            }
        } catch (RequestException $exception) {
        }
        return null;
    }
}
