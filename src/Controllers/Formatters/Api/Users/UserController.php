<?php

namespace Bonnier\Willow\Base\Controllers\Formatters\Api\Users;

use Bonnier\Willow\Base\Controllers\Formatters\Api\AbstractApiController;
use Bonnier\Willow\Base\Controllers\Formatters\Api\ApiControllerContract;
use Bonnier\Willow\Base\Factories\DataFactory;
use Bonnier\Willow\Base\Models\Base\Root\Author;
use Bonnier\Willow\Base\Transformers\Api\Root\AuthorTransformer;

class UserController extends AbstractApiController
{
    protected $transformerMapping = [
        'default' => AuthorTransformer::class
    ];
    protected $baseModelClass = Author::class;

    public function setModel($model): ApiControllerContract
    {
        return parent::setModel(DataFactory::instance()->getUser($model));
    }
}
