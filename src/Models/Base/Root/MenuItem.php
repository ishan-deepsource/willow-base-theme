<?php

namespace Bonnier\Willow\Base\Models\Base\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\MenuItemContract;
use Illuminate\Support\Collection;

class MenuItem implements MenuItemContract
{
    protected $menuItem;

    public function __construct(MenuItemContract $menuItem)
    {
        $this->menuItem = $menuItem;
    }

    public function getId(): ?int
    {
        return $this->menuItem->getId();
    }

    public function getUrl(): ?string
    {
        return $this->menuItem->getUrl();
    }

    public function getTitle(): ?string
    {
        return $this->menuItem->getTitle();
    }

    public function getTarget(): ?string
    {
        return $this->menuItem->getTarget();
    }

    public function getClass(): ?string
    {
        return $this->menuItem->getClass();
    }

    public function getLinkRelationship(): ?string
    {
        return $this->menuItem->getLinkRelationship();
    }

    public function getDescription(): ?string
    {
        return $this->menuItem->getDescription();
    }

    public function getType(): ?string
    {
        return $this->menuItem->getType();
    }

    public function getChildren(): Collection
    {
        return $this->menuItem->getChildren();
    }
}
