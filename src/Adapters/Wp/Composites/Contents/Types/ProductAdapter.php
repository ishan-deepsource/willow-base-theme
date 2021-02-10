<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials\ProductDetailsAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials\ProductDetailsItemAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\Partials\ProductItemAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\ImageAdapter;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials\ProductDetails;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials\ProductDetailsItem;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Partials\ProductItem;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\ProductDetailsContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\ProductDetailsItemContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\ProductItemContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ProductContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Illuminate\Support\Collection;

class ProductAdapter extends AbstractContentAdapter implements ProductContract
{
    public function getTitle(): ?string
    {
        return array_get($this->acfArray, 'title') ?: null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->acfArray, 'description') ?: null;
    }

    public function getImage(): ?ImageContract
    {
        if ($imageArray = array_get($this->acfArray, 'image')) {
            $image = WpModelRepository::instance()->getPost($imageArray);
            return new Image(new ImageAdapter($image));
        }

        return null;
    }

    public function getPrice(): ?string
    {
        return array_get($this->acfArray, 'price') ?: null;
    }

    public function getWinner(): ?bool
    {
        return boolval(array_get($this->acfArray, 'winner', false));
    }

    public function getBestBuy(): ?bool
    {
        return boolval(array_get($this->acfArray, 'best_buy', false));
    }

    public function getMaxPoints(): ?int
    {
        return array_get($this->acfArray, 'max_points') ?: null;
    }

    public function getItems(): Collection
    {
        return collect(array_get($this->acfArray, 'items', []))->map(function ($item) {
            return new ProductItem(new ProductItemAdapter($item));
        })->reject(function (ProductItemContract $item) {
            return is_null($item->getParameter()) &&
                is_null($item->getScore());
        });
    }

    public function getDetailsDescription(): ?string
    {
        return array_get($this->acfArray, 'details_description') ?: null;
    }

    public function getDetailsItems(): Collection
    {
        return collect(array_get($this->acfArray, 'details_items', []))->map(function ($item) {
            return new ProductDetailsItem(new ProductDetailsItemAdapter($item));
        })->reject(function (ProductDetailsItemContract $item) {
            return is_null($item->getDisplayHint()) &&
                is_null($item->getKey()) &&
                is_null($item->getValue());
        });
    }
}
