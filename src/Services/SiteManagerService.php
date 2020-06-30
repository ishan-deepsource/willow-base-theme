<?php

namespace Bonnier\Willow\Base\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Str;

class SiteManagerService extends Client
{
    protected static $instance = null;

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $options = ['base_uri' => env('SITE_MANAGER_HOST')];
        if (Str::contains($options['base_uri'], 'http://staging.')) {
            $options['curl'] = [
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0
            ];
        }
        parent::__construct($options);
    }

    /**
     * @return Client $client
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}
