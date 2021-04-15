<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\AssociatedCompositesContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;
use League\Fractal\TransformerAbstract;

class AssociatedCompositesTransformer extends TransformerAbstract
{
    public function transform(AssociatedCompositesContract $associatedComposites)
    {
        return [
            'title' => $associatedComposites->getTitle(),
            'composites' => $this->transformComposites($associatedComposites),
            'display_hint' => $associatedComposites->getDisplayHint(),
        ];
    }

    private function transformComposites(AssociatedCompositesContract $associatedComposites)
    {
        if ($composites = $associatedComposites->getComposites()) {
            return $composites->map(function (CompositeContract $composite) {
                return with(new CompositeTeaserTransformer)->transform($composite);
            });
        }
        return null;
    }
}
