<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\AssociatedContentContract;

/**
 * Class AssociatedContent
 *
 * @package \Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 * @property AssociatedContentContract $model
 */
class AssociatedContent extends AbstractContent implements AssociatedContentContract
{
    protected $type;
    protected $model;
    protected $locked;

    public function __construct(AssociatedContentContract $associatedContent)
    {
        parent::__construct($associatedContent);
    }

    public function getType() : string
    {
        return $this->model->getType();
    }

    public function isLocked() : bool
    {
        return $this->model->isLocked();
    }

    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }

    public function getAssociatedComposite(): ?CompositeContract
    {
        return $this->model->getAssociatedComposite();
    }
}
