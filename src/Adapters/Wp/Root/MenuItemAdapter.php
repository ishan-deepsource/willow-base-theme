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
        return data_get($this->menuItem, 'object_id') ?: null;
    }

    public function getUrl(): ?string
    {
        return data_get($this->menuItem, 'url') ?: null;
    }

    public function getTitle(): ?string
    {
        return data_get($this->menuItem, 'title') ?: null;
    }

    public function getTarget(): ?string
    {
        return data_get($this->menuItem, 'target') ?: null;
    }

    public function getClass(): ?string
    {
        if ($classes = data_get($this->menuItem, 'classes') ?: null) {
            return implode(' ', $classes) ?: null;
        }

        return null;
    }

    public function getLinkRelationship(): ?string
    {
        return data_get($this->menuItem, 'xfn') ?: null;
    }

    public function getDescription(): ?string
    {
        return data_get($this->menuItem, 'description') ?: null;
    }

    public function getType(): ?string
    {
        return data_get($this->menuItem, 'object') ?: null;
    }

    public function getChildren(): Collection
    {
        return collect(data_get($this->menuItem, 'children'))->map(function ($child) {
            return new static($child);
        });
    }
}
