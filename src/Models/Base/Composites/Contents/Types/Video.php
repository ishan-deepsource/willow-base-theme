<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\VideoContract;

/**
 * Class Link
 *
 * @property VideoContract $model
 *
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 */
class Video extends AbstractContent implements VideoContract
{
    /**
     * Link constructor.
     *
     * @param \Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\VideoContract $video
     */
    public function __construct(VideoContract $video)
    {
        parent::__construct($video);
    }

    public function getEmbedUrl(): ?string
    {
        return $this->model->getEmbedUrl();
    }

    public function getCaption(): ?string
    {
        return $this->model->getCaption();
    }
    
    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }
}
