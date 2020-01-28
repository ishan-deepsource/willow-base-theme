<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\NewsletterContract;
use League\Fractal\TransformerAbstract;

/**
 * Class NewsletterTransformer
 *
 * @package \Bonnier\Willow\Base\Transformers\Api\Composites\Partials
 */
class NewsletterTransformer extends TransformerAbstract
{
    public function transform(NewsletterContract $newsletter)
    {
        return [
            'title' => $newsletter->getTitle(),
            'description' => $newsletter->getDescription(),
            'source_code' => $newsletter->getSourceCode(),
            'permission_text' => $newsletter->getPermissionText(),
        ];
    }
}
