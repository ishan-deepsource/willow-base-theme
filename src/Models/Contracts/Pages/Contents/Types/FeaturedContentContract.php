<?php

namespace Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\NativeVideoContract;

interface FeaturedContentContract extends ContentContract
{
    public function getImage(): ?ImageContract;
    public function getVideo(): ?NativeVideoContract;
    public function getDisplayHint(): ?string;
    public function getComposite(): ?CompositeContract;
}
