<?php

namespace Bonnier\Willow\Base\Models\Contracts\Root;

use Illuminate\Support\Collection;

interface MenuItemContract
{
    public function getId(): ?int;

    public function getUrl(): ?string;

    public function getTitle(): ?string;

    public function getTarget(): ?string;

    public function getType(): ?string;

    public function getChildren(): Collection;
}
