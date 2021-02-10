<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\ProductDetailsItemContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\ProductItemContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ProductContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\Partials\ProductDetailsItemTransformer;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\Partials\ProductItemTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    public function transform(ProductContract $product)
    {
        return [
            'title' => $product->getTitle(),
            'image' => $this->transformImage($product),
            'price' => $product->getPrice(),
            'winner' => $product->getWinner(),
            'best_buy' => $product->getBestBuy(),
            'max_points' => $product->getMaxPoints(),
            'items' => $this->transformItems($product),
            'description' => $product->getDescription(),
            'details_description' => $product->getDetailsDescription(),
            'details_items' => $this->transformDetailsItems($product),
        ];
    }

    private function transformImage(ProductContract $product)
    {
        if ($image = $product->getImage()) {
            return with(new ImageTransformer)->transform($image);
        }

        return null;
    }

    private function transformItems(ProductContract $product)
    {
        return $product->getItems()->map(function (ProductItemContract $productItem) {
            return with(new ProductItemTransformer())->transform($productItem);
        });
    }

    private function transformDetailsItems(ProductContract $product)
    {
        return $product->getDetailsItems()->map(function (ProductDetailsItemContract $productDetailsItem) {
            return with(new ProductDetailsItemTransformer())->transform($productDetailsItem);
        });
    }
}
