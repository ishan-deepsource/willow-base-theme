<?php

namespace Bonnier\Willow\Base\Controllers\Formatters\Api\Page;

use Bonnier\Willow\Base\Controllers\Formatters\Api\AbstractApiController;
use Bonnier\Willow\Base\Models\Base\Pages\Page;
use Bonnier\Willow\Base\Transformers\Api\Pages\PageTransformer;

class PageController extends AbstractApiController
{
    protected $transformerMapping = [
        'default' => PageTransformer::class
    ];
    protected $baseModelClass = Page::class;
}
