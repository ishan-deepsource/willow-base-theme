<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials;

interface ProductDetailsItemContract
{
    public function getDisplayHint(): ?string;

    public function getKey(): ?string;

    public function getValue(): ?string;
}
