<?php

namespace Bonnier\Willow\Base\Repositories\SiteManager;

use Bonnier\Willow\Base\Repositories\Contracts\SiteManager\TaxonomyContract;
use Bonnier\Willow\Base\Services\SiteManagerService;
use GuzzleHttp\Exception\ClientException;

/**
 * Class SiteRepository
 */
class CategoryRepository implements TaxonomyContract
{
    public static function get_all($page = 1)
    {
        try {
            $response = SiteManagerService::getInstance()->get('/api/v1/categories', [
                'query' => [
                    'page' => $page
                ]
            ]);
        } catch (ClientException $e) {
            return [];
        }
        return $response->getStatusCode() === 200 ?
            json_decode($response->getBody()->getContents())->data :
            [];
    }

    public static function find_by_id($id)
    {
        try {
            $response = SiteManagerService::getInstance()->get('/api/v1/categories/'.$id);
        } catch (ClientException $e) {
            return null;
        }
        return $response->getStatusCode() === 200 ?
            json_decode($response->getBody()->getContents()) :
            null;
    }

    public static function find_by_content_hub_id($id)
    {
        try {
            $response = SiteManagerService::getInstance()->get('/api/v1/categories/content-hub-id/'.$id);
        } catch (ClientException $e) {
            return null;
        }
        return $response->getStatusCode() === 200 ?
            json_decode($response->getBody()->getContents()) :
            null;
    }

    public static function find_by_brand_id($id, $page = 1)
    {
        try {
            $response = SiteManagerService::getInstance()->get('/api/v1/categories/brand/' . $id, [
                'query' => [
                    'page' => $page
                ]
            ]);
        } catch (ClientException $e) {
            return null;
        }
        return $response->getStatusCode() === 200 ?
            json_decode($response->getBody()->getContents()) :
            null;
    }
}
