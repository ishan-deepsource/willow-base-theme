<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;

/**
 * Class AbstractContent
 *
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents
 */
abstract class AbstractContent implements ContentContract
{
    protected $type;
    protected $locked;
    protected $model;

    /**
     * AbstractContent constructor.
     *
     * @param ContentContract $content
     */
    public function __construct(ContentContract $content)
    {
        $this->type = $content->getType();
        $this->locked = $content->isLocked();
        $this->model = $content;
    }

    public function getType() : string
    {
        return $this->type;
    }

    public function isLocked() : bool
    {
        return $this->locked;
    }
}
