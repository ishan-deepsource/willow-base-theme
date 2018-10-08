<?php

namespace Bonnier\Willow\Base\Models\Base\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Pages\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\VideoContract;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\FeaturedContentContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\NativeVideoContract;

/**
 * Class FeaturedContent
 * @package Bonnier\Willow\Base\Models\Base\Pages\Contents\Types
 * @property FeaturedContentContract $model
 */
class FeaturedContent extends AbstractContent implements FeaturedContentContract
{
    public function getImage(): ?ImageContract
    {
        return $this->model->getImage();
    }

    public function getVideo(): ?NativeVideoContract
    {
        return $this->model->getVideo();
    }

    public function getDisplayHint(): ?string
    {
        return $this->model->getDisplayHint();
    }

    public function getComposite(): ?CompositeContract
    {
        return $this->model->getComposite();
    }
}
