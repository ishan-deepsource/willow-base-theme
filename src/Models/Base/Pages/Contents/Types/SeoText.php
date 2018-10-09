<?php

namespace Bonnier\Willow\Base\Models\Base\Pages\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Pages\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\VideoContract;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\FeaturedContentContract;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\SeoTextContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\NativeVideoContract;

/**
 * Class SeoText
 * @package Bonnier\Willow\Base\Models\Base\Pages\Contents\Types
 * @property FeaturedContentContract $model
 */
class SeoText extends AbstractContent implements SeoTextContract
{

    public function getTitle(): ?string
    {
        return $this->model->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->model->getDescription();
    }

    public function getImage(): ?ImageContract
    {
        return $this->model->getImage();
    }
}
