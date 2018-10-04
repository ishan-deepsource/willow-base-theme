<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\HyperlinkContract;

/**
 * Class Image
 *
 * @property ContentImageContract $model
 *
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 */
class ContentImage extends AbstractContent implements ContentImageContract
{
    /**
     * Image constructor.
     *
     * @param \Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentImageContract $image
     */
    public function __construct(ContentImageContract $image)
    {
        parent::__construct($image);
    }

    public function getUrl() : ?string
    {
        return $this->model->getUrl();
    }

    public function getCaption() : ?string
    {
        return $this->model->getCaption();
    }

    public function getId() : ?int
    {
        return $this->model->getId();
    }

    public function isLead(): bool
    {
        return $this->model->isLead();
    }

    public function getTitle(): ?string
    {
        return $this->model->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->model->getDescription();
    }

    public function getAlt(): ?string
    {
        return $this->model->getAlt();
    }

    public function getCopyright(): ?string
    {
        return $this->model->getCopyright();
    }

    public function getLanguage(): ?string
    {
        return $this->model->getLanguage();
    }

    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }

    public function getFocalPoint(): array
    {
        return $this->model->getFocalPoint();
    }

    public function getAspectRatio(): float
    {
        return $this->model->getAspectRatio();
    }

    public function getLink(): ?HyperlinkContract
    {
        return $this->model->getLink();
    }

    public function getDisplayHint(): ?string
    {
        return $this->model->getDisplayHint();
    }
}
