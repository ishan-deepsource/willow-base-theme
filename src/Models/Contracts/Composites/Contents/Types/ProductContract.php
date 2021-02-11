<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Illuminate\Support\Collection;

interface ProductContract extends ContentContract
{
    public function getTitle(): ?string;

    public function getDescription(): ?string;

    public function getImage(): ?ImageContract;

    public function getPrice(): ?string;

    public function getWinner(): ?bool;

    public function getBestBuy(): ?bool;

    public function getMaxPoints(): ?int;

    public function getItems(): Collection;

    public function getDetailsTitle(): ?string;

    public function getDetailsDescription(): ?string;

    public function getDetailsItems(): Collection;
}
