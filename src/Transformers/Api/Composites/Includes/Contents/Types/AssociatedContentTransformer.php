<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\AssociatedContentContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;

/**
 * Class ContentAssociatedComposite
 *
 * @package \Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types
 */
class AssociatedContentTransformer
{
    public function transform(AssociatedContentContract $associatedContent)
    {
        return [
            'composite' => $this->getAssociated($associatedContent),
        ];
    }

    public function getAssociated($associatedContent){
        return with(new CompositeTeaserTransformer())->transform(new Composite(new CompositeAdapter($associatedContent->getAssociatedComposite())));
    }
}
