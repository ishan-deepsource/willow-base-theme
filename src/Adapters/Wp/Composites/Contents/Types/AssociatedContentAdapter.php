<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\AssociatedContentContract;

/**
 * Class AssociatedContentAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types
 */
class AssociatedContentAdapter extends AbstractContentAdapter implements AssociatedContentContract
{
    public function __construct(array $acfArray)
    {
        parent::__construct($acfArray);
    }

    public function getType(): ?string
    {
        return array_get($this->acfArray, 'acf_fc_layout') ?: null;
    }

    public function isLocked(): bool
    {
        return array_get($this->acfArray, 'locked_content', false);
    }

    public function getStickToNext(): bool
    {
        return array_get($this->acfArray, 'stick_to_next', false);
    }

    public function getAssociatedComposite(): ?CompositeContract
    {
        if (($post = array_get($this->acfArray, 'composite.0')) && $post instanceof \WP_Post) {
            return new Composite(new CompositeAdapter($post));
        }

        return null;
    }
}
