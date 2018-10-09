<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;


use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\LeadParagraphContract;
use League\Fractal\TransformerAbstract;

class LeadParagraphTransformer extends TransformerAbstract
{
    public function transform(LeadParagraphContract $leadParagraph)
    {
        return [
            'title' => $leadParagraph->getTitle(),
            'description' => $leadParagraph->getDescription(),
            'display_hint' => $leadParagraph->getDisplayHint(),
        ];
    }
}
