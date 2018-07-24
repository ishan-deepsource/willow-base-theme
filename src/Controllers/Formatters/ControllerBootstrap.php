<?php

namespace Bonnier\Willow\Base\Controllers\Formatters;

use Bonnier\Willow\Base\Controllers\Formatters\Api\ApiControllerContract;
use Bonnier\Willow\Base\Controllers\Formatters\Api\Composite\CompositeController;
use Bonnier\Willow\Base\Controllers\Formatters\Api\Page\PageController;
use Bonnier\Willow\Base\Controllers\Formatters\Api\Terms\Category\CategoryController;
use Bonnier\Willow\Base\Controllers\Formatters\Api\Terms\Tag\TagController;
use Bonnier\Willow\Base\Controllers\Formatters\Api\Users\UserController;
use League\Fractal\Manager;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Class ControllerBootstrap
 *
 * @package \Bonnier\Willow\Base\Controllers\Formatters
 */
class ControllerBootstrap
{
    protected $apiControllers = [
        'page'                 => PageController::class,
        'category'             => CategoryController::class,
        'post_tag'             => TagController::class,
        'contenthub_composite' => CompositeController::class,
        'user'                 => UserController::class
    ];

    /**
     * ControllerBootstrap constructor.
     */
    public function __construct()
    {
        $this->registerApiControllers();
    }

    private function registerApiControllers()
    {
        collect($this->apiControllers)->each(function ($controllerClass, $contentType) {
            add_filter("rest_prepare_${contentType}", function ($response, $model, $request) use ($controllerClass) {
                return $this->getControllerInstance($controllerClass, $response, $request)
                    ->setModel($model)
                    ->getResponse();
            }, 10, 3);
        });
    }

    /**
     * @param                   $controller
     * @param \WP_REST_Response $response
     * @param \WP_REST_Request  $request
     *
     * @return \Bonnier\Willow\Base\Controllers\Formatters\Api\ApiControllerContract
     */
    private function getControllerInstance(
        $controller,
        WP_REST_Response $response,
        WP_REST_Request $request
    ): ApiControllerContract {
        return new $controller(new Manager(), $response, $request);
    }
}
