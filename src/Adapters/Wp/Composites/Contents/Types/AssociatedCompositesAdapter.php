<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\AssociatedCompositesContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\AssociatedContentContract;
use Illuminate\Support\Collection;

/**
 * Class AssociatedContentAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types
 */
class AssociatedCompositesAdapter extends AbstractContentAdapter implements AssociatedCompositesContract
{
    public function __construct(array $acfArray)
    {
        parent::__construct($acfArray);
    }

    public function getComposites(): ?Collection
    {
        $composites = collect(array_get($this->acfArray, 'composites', []))
            ->map(function (\WP_Post $composite) {
                return new Composite(new CompositeAdapter($composite));
            });

        return $composites->isNotEmpty() ? $composites : null;
    }
}
