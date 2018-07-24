<?php

namespace Bonnier\Willow\Base\Controllers\Formatters\Api\Terms\Tag;

use Bonnier\Willow\Base\Controllers\Formatters\Api\AbstractApiController;
use Bonnier\Willow\Base\Models\Base\Terms\Tag;
use Bonnier\Willow\Base\Transformers\Api\Terms\Tag\TagTransformer;

/**
 * Class CategoryController
 *
 * @package \Bonnier\Willow\Base\Controllers\Formatters\Terms
 */
class TagController extends AbstractApiController
{
    protected $transformerMapping = [
        'default' => TagTransformer::class
    ];
    protected $baseModelClass = Tag::class;
}
