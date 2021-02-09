<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials;

interface ProductItemContract
{
    public function getParameter(): ?string;

    public function getScore(): ?string;
}
