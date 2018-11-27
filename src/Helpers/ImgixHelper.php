<?php

namespace Bonnier\Willow\Base\Helpers;

use GuzzleHttp\Client;

class ImgixHelper
{
    public static $client;

    public static function getColorPalette(string $url)
    {
        return self::get($url . '?palette=json');
    }

    private static function get($url)
    {
        try {
            $response = self::getClient()->get($url);
            $data = json_decode($response->getBody()->getContents());
            return json_last_error() === JSON_ERROR_NONE ? $data : null;
        } catch (\Exception $exception) {
        }
        return null;
    }

    private static function getClient(): Client
    {
        if (!self::$client) {
            self::$client = new Client();
        }

        return self::$client;
    }
}
