<?php

namespace Bonnier\Willow\Base\Controllers\Api;

class FocalpointEndpointController extends \WP_REST_Controller
{
    public function register_routes()
    {
        add_action('rest_api_init', function () {
            register_rest_route('content-hub-editor', '/focalpoint/(?P<id>[0-9]+)', [
                'methods' => \WP_REST_Server::READABLE,
                'callback' => [$this, 'get']
            ]);
            register_rest_route('content-hub-editor', '/focalpoint/(?P<id>[0-9]+)', [
                'methods' => \WP_REST_Server::EDITABLE,
                'callback' => [$this, 'update']
            ]);
        });
    }

    public function get(\WP_REST_Request $request)
    {
        if (is_user_logged_in()) {
            return new \WP_REST_Response(['status' => 'unauthorized'], 401);
        }
        $id = $request->get_param('id');
        if (!wp_attachment_is_image($id)) {
            return new \WP_REST_Response(['status' => 'Not Found'], 404);
        }
        $focalpoint = get_post_meta($id, '_focal_point', true);

        $xCoord = $yCoord = '0.50';

        if ($focalpoint) {
            list($xCoord, $yCoord) = explode(',', $focalpoint);
        }

        return new \WP_REST_Response(['focalpoint' => ['x' => $xCoord, 'y' => $yCoord]]);
    }

    public function update(\WP_REST_Request $request)
    {
        if (is_user_logged_in()) {
            return new \WP_REST_Response(['status' => 'unauthorized'], 401);
        }
        $id = $request->get_param('id');
        if (!wp_attachment_is_image($id)) {
            return new \WP_REST_Response(['status' => 'Not Found'], 404);
        }
        $xCoord = floatval($request->get_param('x'));
        $yCoord = floatval($request->get_param('y'));

        if (($xCoord > 1) || ($yCoord > 1)) {
            return new \WP_REST_Response(['status' => 'Invalid focalpoint'], 422);
        }

        if (update_post_meta($id, '_focal_point', sprintf('%.2f,%.2f', $xCoord, $yCoord))) {
            return new \WP_REST_Response(['success' => true]);
        }

        return new \WP_REST_Response(['success' => false], 500);
    }
}
