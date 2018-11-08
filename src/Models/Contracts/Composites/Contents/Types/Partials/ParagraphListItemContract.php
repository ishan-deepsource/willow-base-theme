<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials;

use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

interface ParagraphListItemContract
{
    public function getTitle(): ?string;

    public function getDescription(): ?string;

    public function getImage(): ?ImageContract;
}
