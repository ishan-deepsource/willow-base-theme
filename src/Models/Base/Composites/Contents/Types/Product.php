<?php

namespace Bonnier\Willow\Base\Models\Base\Composites\Contents\Types;

use Bonnier\Willow\Base\Models\Base\Composites\Contents\AbstractContent;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ProductContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Illuminate\Support\Collection;

/**
 * Class Product
 * @package Bonnier\Willow\Base\Models\Base\Composites\Contents\Types
 * @property ProductContract $model
 */
class Product extends AbstractContent implements ProductContract
{
    public function __construct(ProductContract $content)
    {
        parent::__construct($content);
    }

    public function getStickToNext(): bool
    {
        return $this->model->getStickToNext();
    }

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

    public function getPrice(): ?string
    {
        return $this->model->getPrice();
    }

    public function getWinner(): ?bool
    {
        return $this->model->getWinner();
    }

    public function getBestBuy(): ?bool
    {
        return $this->model->getBestBuy();
    }

    public function getMaxPoints(): ?int
    {
        return $this->model->getMaxPoints();
    }

    public function getItems(): Collection
    {
        return $this->model->getItems();
    }

    public function getDetailsTitle(): ?string
    {
        return $this->model->getDetailsTitle();
    }

    public function getDetailsDescription(): ?string
    {
        return $this->model->getDetailsDescription();
    }

    public function getDetailsItems(): Collection
    {
        return $this->model->getDetailsItems();
    }
}
