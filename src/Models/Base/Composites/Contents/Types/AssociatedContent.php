<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;

/**
 * Class AssociatedContent
 *
 * @package \Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 */
class AssociatedContent extends Composite implements ContentContract
{
    protected $type;
    protected $model;
    protected $locked;

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

    public function getStickToNext(): bool
    {
        return false;
    }
}
