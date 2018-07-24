<?php

namespace Bonnier\Willow\Base\Controllers\Formatters\Api;

use Bonnier\Willow\Base\Exceptions\Controllers\Api\BaseModelMissingException;
use Bonnier\Willow\Base\Exceptions\Controllers\Api\TransformerMappingMissingException;
use Bonnier\Willow\Base\Factories\Contracts\ModelFactoryContract;
use Bonnier\Willow\Base\Factories\WPModelFactory;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use WP_REST_Request;
use WP_REST_Response;

abstract class AbstractApiController implements ApiControllerContract
{
    const INCLUDES_QUERY_PARAM = 'with';
    const EXCLUDES_QUERY_PARAM = 'without';
    const FORMAT_QUERY_PARAM = 'format';

    protected $fractal;
    protected $response;
    protected $model;
    protected $request;
    /* @var array must at least have a default => SomeTransformer::class for the given controller */
    protected $transformerMapping = [];
    /* @var mixed should be set to the default base model::class for the given controller */
    protected $baseModelClass;

    /**
     * AbstractApiController constructor.
     *
     * @param \League\Fractal\Manager $fractal
     * @param \WP_REST_Response       $response
     * @param \WP_REST_Request        $request
     *
     * @throws \Bonnier\Willow\Base\Exceptions\Controllers\Api\TransformerMappingMissingException
     * @throws \Bonnier\Willow\Base\Exceptions\Controllers\Api\BaseModelMissingException
     */
    public function __construct(Manager $fractal, WP_REST_Response $response, WP_REST_Request $request)
    {
        $this->fractal = $fractal;
        $this->response = $response;
        $this->request = $request;
        if (empty($this->transformerMapping) || ! isset($this->transformerMapping['default'])) {
            throw new TransformerMappingMissingException(
                sprintf('Missing "default" transformer in $transformerMapping on class: %s', static::class)
            );
        }
        if (empty($this->baseModelClass)) {
            throw new BaseModelMissingException(
                sprintf('Missing $baseModelClass on class: %s', static::class)
            );
        }
    }

    /**
     */
    public function getModelFactory(): ModelFactoryContract
    {
        return new WPModelFactory($this->baseModelClass);
    }

    /**
     * @param $model
     *
     * @return \Bonnier\Willow\Base\Controllers\Formatters\Api\ApiControllerContract
     */
    public function setModel($model): ApiControllerContract
    {
        $this->model = $this->getModelFactory()->getModel($model);
        return $this;
    }

    public function getResponse()
    {
        $this->removeLinks();
        $this->validateFormat();
        $this->fractal->parseIncludes($this->request->get_param(static::INCLUDES_QUERY_PARAM) ?? '');
        $this->fractal->parseExcludes($this->request->get_param(static::EXCLUDES_QUERY_PARAM) ?? '');
        $this->response->data = $this->fractal
            ->createData(new Item($this->model, $this->getTransformer()))
            ->toArray();
        return $this->response;
    }

    public function getTransformer()
    {
        $format = $this->request->get_param(static::FORMAT_QUERY_PARAM);
        $transformerClass = collect($this->transformerMapping)->get(
            $format,
            collect($this->transformerMapping)->get('default')
        );
        return new $transformerClass($this->response->get_data());
    }

    private function validateFormat()
    {
        $format = $this->request->get_param(static::FORMAT_QUERY_PARAM);
        if (! collect($this->transformerMapping)->get($format) && ! empty($format)) {
            $errorMsg = sprintf(
                'Invalid format provided: %s, valid formats are: %s',
                $format,
                json_encode(array_keys($this->transformerMapping))
            );
            status_header(422, $errorMsg);
            exit(json_encode([
                'Error' => $errorMsg
            ]));
        }
    }

    private function removeLinks()
    {
        collect($this->response->get_links())->each(function ($link, $key) {
            $this->response->remove_link($key);
        });
    }
}
