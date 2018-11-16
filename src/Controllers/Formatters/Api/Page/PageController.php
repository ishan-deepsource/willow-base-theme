<?php

namespace Bonnier\Willow\Base\Controllers\Formatters\Api\Page;

use Bonnier\Willow\Base\Controllers\Formatters\Api\AbstractApiController;
use Bonnier\Willow\Base\Controllers\Formatters\Api\ApiControllerContract;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Models\Base\Pages\Page;
use Bonnier\Willow\Base\Transformers\Api\Pages\PageTransformer;

class PageController extends AbstractApiController
{
    protected $transformerMapping = [
        'default' => PageTransformer::class
    ];
    protected $baseModelClass = Page::class;

    public function setModel($model): ApiControllerContract
    {
        return parent::setModel(WpModelRepository::instance()->getPost($model));
    }
}
