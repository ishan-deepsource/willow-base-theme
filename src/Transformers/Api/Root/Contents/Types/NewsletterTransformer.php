<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\NewsletterContract;
use League\Fractal\TransformerAbstract;

class NewsletterTransformer extends TransformerAbstract
{
    public function transform(NewsletterContract $newsletter)
    {
        return [
            'source_code' => $newsletter->getSourceCode(),
            'permission_text' => $newsletter->getPermissionText(),
        ];
    }
}
