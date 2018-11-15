<?php

namespace Bonnier\Willow\Base\Controllers\Formatters\Api\Terms\Category;

use Bonnier\Willow\Base\Controllers\Formatters\Api\AbstractApiController;
use Bonnier\Willow\Base\Controllers\Formatters\Api\ApiControllerContract;
use Bonnier\Willow\Base\Factories\DataFactory;
use Bonnier\Willow\Base\Models\Base\Terms\Category;
use Bonnier\Willow\Base\Transformers\Api\Terms\Category\CategoryTransformer;

/**
 * Class CategoryController
 *
 * @package \Bonnier\Willow\Base\Controllers\Formatters\Terms
 */
class CategoryController extends AbstractApiController
{
    protected $transformerMapping = [
        'default' => CategoryTransformer::class
    ];
    protected $baseModelClass = Category::class;

    public function setModel($model): ApiControllerContract
    {
        return parent::setModel(DataFactory::instance()->getTerm($model));
    }
}
