<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\MenuItemContract;
use Illuminate\Support\Collection;

/**
 * Class MenuItemAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class MenuItemAdapter implements MenuItemContract
{
    protected $menuItem;

    public function __construct($menuItem)
    {
        $this->menuItem = $menuItem;
        $this->getChildren();
    }

    public function getId(): ?int
    {
        return $this->menuItem->object_id ?? null;
    }

    public function getUrl(): ?string
    {
        return $this->menuItem->url ?? null;
    }

    public function getTitle(): ?string
    {
        return $this->menuItem->title ?? null;
    }

    public function getTarget(): ?string
    {
        return $this->menuItem->target ?? null;
    }

    public function getType(): ?string
    {
        return $this->menuItem->object ?? null;
    }

    public function getChildren(): Collection
    {
        return collect($this->menuItem->children ?? [])->map(function ($child) {
            return new static($child);
        });
    }
}
