<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\MenuItemContract;
use Bonnier\Willow\Base\Traits\UrlTrait;
use League\Fractal\TransformerAbstract;

/**
 * Class FileTransformer
 *
 * @package \Bonnier\Willow\Base\Transformers\Api\Composites\Partials
 */
class MenuItemTransformer extends TransformerAbstract
{
    use UrlTrait;

    public function transform(MenuItemContract $menuItem)
    {
        return [
            'id' => $menuItem->getId(),
            'url' => $this->getPath($menuItem->getUrl()),
            'title' => $menuItem->getTitle(),
            'target' => $menuItem->getTarget(),
            'type' => $menuItem->getType(),
            'children' => $menuItem->getChildren()->map(function (MenuItemContract $childMenuItem) {
                return with(new MenuItemTransformer)->transform($childMenuItem);
            })
        ];
    }
}
