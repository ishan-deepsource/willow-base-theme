<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;

interface MultimediaContract extends ContentContract
{
    public function getTitle(): ?string;

    public function getDescription(): ?string;

    public function getImage(): ?ImageContract;

    public function getDisplayHint(): ?string;

    public function getVectaryId(): ?string;

    public function getVectaryUrl(): ?string;
}
