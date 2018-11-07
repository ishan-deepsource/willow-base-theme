<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\AssociatedCompositesContract;
use Illuminate\Support\Collection;

/**
 * Class AssociatedContent
 *
 * @package \Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 * @property AssociatedCompositesContract $model
 */
class AssociatedComposites extends AbstractContent implements AssociatedCompositesContract
{
    public function __construct(AssociatedCompositesContract $associatedContent)
    {
        parent::__construct($associatedContent);
    }

    public function getComposites(): ?Collection
    {
        return $this->model->getComposites();
    }

    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }
}
