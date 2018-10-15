<?php

namespace Bonnier\Willow\Base\Controllers\App;

use WP_REST_Controller;
use WP_REST_Response;

class PermissionTextTest extends BaseController
{
    //TODO DELETE THIS ONCE WILL-384 is done
    public function register_routes()
    {
        register_rest_route('app', '/newsletter-endpoint', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, 'signup']
        ]);
    }


    /**
     * @return WP_REST_Response
     */
    public function signup()
    {
        $response = [];
        $_POST = json_decode(file_get_contents('php://input'), true);


        if (!isset($_POST['firstName']) || empty($_POST['firstName'])) {
            $response['message'] = "The given data was invalid.";
            $response['errors']['firstName'][0] = "The name field is required.";
        }

        if (!isset($_POST['email']) || empty($_POST['email'])) {
            $response['message'] = "The given data was invalid.";
            $response['errors']['email'][0] = "The email field is required.";
        }

        if (isset($response['errors'])) {
            header("Status: 422 Unprocessable Entity");
        } else {
            $response['message'] = "Newsletter Sent!";
        }

        sleep(2);

        return new WP_REST_Response($response);
    }
}
