<?php

namespace Bonnier\Willow\Base\Controllers\App;

class SettingsController extends \WP_REST_Controller
{
    public function register_routes()
    {
        register_rest_route('app/settings', '/native-version', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, 'getNativeVersion'],
        ]);
    }

    /**
     * Returns the current supported version number for the native apps.
     * If it's changed it will force all users of the app to update it,
     * before it can be used. Therefore we are hardcoding the response
     * so that we are less likely to accidentally change it.
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function getNativeVersion(\WP_REST_Request $request)
    {
        return new \WP_REST_Response([
            'supported_version' => [
                'android' => '1.0.0',
                'ios' => '1.0.0'
            ],
        ]);
    }
}
