<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\ParagraphListItemContract;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use League\Fractal\TransformerAbstract;

class ParagraphListItemTransformer extends TransformerAbstract
{
    public function transform(ParagraphListItemContract $paragraphListItem)
    {
        return [
            'title' => $paragraphListItem->getTitle(),
            'description' => $paragraphListItem->getDescription(),
            'image' => $this->transformImage($paragraphListItem),
        ];
    }

    private function transformImage(ParagraphListItemContract $paragraphListItem)
    {
        if ($image = $paragraphListItem->getImage()) {
            return with(new ImageTransformer)->transform($image);
        }

        return null;
    }
}
