<?php

namespace Bonnier\Willow\Base\Controllers\Api;

use Bonnier\Willow\Base\Helpers\TermImportHelper;

class UpdateEndpointController extends \WP_REST_Controller
{
    public function register_routes()
    {
        // We need to set PLL_ADMIN to true, before polylang is initialized
        // But we should only do it, when we are requesting this endpoint.
        // Doing it inside 'rest_api_ini' is too late, so this ugly hack is sadly needed.
        if (
            $_SERVER['REQUEST_URI'] === '/wp-json/content-hub-editor/updates' &&
            $_SERVER['REQUEST_METHOD'] === 'POST' &&
            !defined('PLL_ADMIN')
        ) {
            define('PLL_ADMIN', true);
        }

        add_action('rest_api_init', function () {
            register_rest_route('content-hub-editor', '/updates', [
                'methods'  => \WP_REST_Server::CREATABLE,
                'callback' => [$this, 'updateCallback'],
            ]);
        });
    }

    public function updateCallback(\WP_REST_Request $request): \WP_REST_Response
    {
        $this->initPolylangShareTermSlug(); // Make sure that category slugs are sharable across languages

        $resource = $this->formatResource($request->get_param('data'));
        $meta = $request->get_param('meta');
        $entityType = $meta['entity_type'];
        $actionType = $meta['action_type'];

        //error_log('RESOURCE FROM UpdateEndpointController: ' . $resource, 0);
        error_log('ENTITY TYPE FROM UpdateEndpointController: ' . $entityType, 0);
        error_log('ACTION TYPE FROM UpdateEndpointController: ' . $actionType, 0);

        if ($resource && $entityType) {
            $termImporter = $this->getTermImporter($entityType);
            if (in_array($actionType, ['create', 'update'])) {
                $result = $termImporter->importTermAndLinkTranslations($resource);
                error_log('RESULT FROM UpdateEndpointController: ' . implode(',', $result), 0);
            }
            if ($actionType === 'delete') {
                $termImporter->deleteTermAndTranslations($resource);
            }
            error_log(PHP_EOL . '####################################################################' . PHP_EOL);
            return new \WP_REST_Response(['status' => 'OK']);
        }

        return new \WP_REST_Response(['error' => 'unknown type'], 400);
    }

    private function getTermImporter($entityType): TermImportHelper
    {
        return new TermImportHelper($entityType);
    }

    private function formatResource($resource)
    {
        // Convert array to object
        return json_decode(json_encode($resource));
    }

    private function initPolylangShareTermSlug()
    {
        $polylang = PLL(); // We have to store in variable because class is using & which is not allowed for objects
        new \PLL_Admin_Share_Term_Slug($polylang);
        new \PLL_Admin_Filters($polylang);
    }
}
