<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ParagraphListContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\ParagraphListItemContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\Partials\ParagraphListItemTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use League\Fractal\TransformerAbstract;

class ParagraphListTransformer extends TransformerAbstract
{
    public function transform(ParagraphListContract $paragraphList)
    {
        return [
            'title' => $paragraphList->getTitle(),
            'description' => $paragraphList->getDescription(),
            'image' => $this->transformImage($paragraphList),
            'video_url' => $paragraphList->getVideoUrl(),
            'collapsible' => $paragraphList->isCollapsible(),
            'display_hint' => $paragraphList->getDisplayHint(),
            'items' => $this->transformItems($paragraphList)
        ];
    }

    private function transformImage(ParagraphListContract $paragraphList)
    {
        if ($image = $paragraphList->getImage()) {
            return with(new ImageTransformer)->transform($image);
        }

        return null;
    }

    private function transformItems(ParagraphListContract $paragraphList)
    {
        return $paragraphList->getItems()->map(function (ParagraphListItemContract $paragraphListItem) {
            return with(new ParagraphListItemTransformer)->transform($paragraphListItem);
        });
    }
}
