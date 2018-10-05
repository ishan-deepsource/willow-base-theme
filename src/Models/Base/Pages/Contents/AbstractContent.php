<?php

namespace Bonnier\Willow\Base\Models\Base\Pages\Contents;

use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\ContentContract;

/**
 * Class AbstractContent
 *
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents
 */
abstract class AbstractContent implements ContentContract
{
    protected $model;

    /**
     * AbstractContent constructor.
     *
     * @param ContentContract $content
     */
    public function __construct(ContentContract $content)
    {
        $this->model = $content;
    }

    public function getType(): ?string
    {
        return $this->model->getType();
    }
}
