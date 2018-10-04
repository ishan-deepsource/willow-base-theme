<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\AssociatedContentContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;
use League\Fractal\TransformerAbstract;

class AssociatedContentTransformer extends TransformerAbstract
{
    public function transform(AssociatedContentContract $associatedContent)
    {
        return [
            'composite' => $this->transformAssociatedContent($associatedContent),
        ];
    }

    private function transformAssociatedContent(AssociatedContentContract $associatedContent)
    {
        if ($composite = $associatedContent->getAssociatedComposite()) {
            return with(new CompositeTeaserTransformer)->transform($composite);
        }

        return null;
    }
}
