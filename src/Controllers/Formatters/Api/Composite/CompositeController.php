<?php

namespace Bonnier\Willow\Base\Controllers\Formatters\Api\Composite;

use Bonnier\Willow\Base\Controllers\Formatters\Api\AbstractApiController;
use Bonnier\Willow\Base\Controllers\Formatters\Api\ApiControllerContract;
use Bonnier\Willow\Base\Factories\DataFactory;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTransformer;

class CompositeController extends AbstractApiController
{
    protected $transformerMapping = [
        'default' => CompositeTransformer::class,
        'teaser'  => CompositeTeaserTransformer::class
    ];
    protected $baseModelClass = Composite::class;

    public function setModel($model): ApiControllerContract
    {
        return parent::setModel(DataFactory::instance()->getPost($model));
    }
}
