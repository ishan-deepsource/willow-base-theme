<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\LinkContract;
use Bonnier\Willow\Base\Traits\UrlTrait;
use League\Fractal\TransformerAbstract;

/**
 * Class LinkTransformer
 *
 * @package \Bonnier\Willow\Base\Transformers\Api\Composites\Partials
 */
class LinkTransformer extends TransformerAbstract
{
    use UrlTrait;

    public function transform(LinkContract $link)
    {
        return [
            'url'    => $link->isLocked() ? null : $this->getPath($link->getUrl()),
            'title'  => $link->isLocked() ? null : $link->getTitle(),
            'target' => $link->isLocked() ? null : $link->getTarget(),
            'display_hint' => $link->isLocked() ? null : $link->getDisplayHint()
        ];
    }
}
