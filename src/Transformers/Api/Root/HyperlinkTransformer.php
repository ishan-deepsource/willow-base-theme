<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;
use League\Fractal\TransformerAbstract;

class HyperlinkTransformer extends TransformerAbstract
{
    public function transform(HyperlinkContract $hyperlink)
    {
        return [
            'title'     => $hyperlink->getTitle(),
            'url'       => $hyperlink->getUrl(),
            'rel'       => $hyperlink->getRelationship(),
            'target'    => $hyperlink->getTarget(),
        ];
    }
}
