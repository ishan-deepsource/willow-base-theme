<?php

namespace Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ParagraphListContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\Partials\ProductItemContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ProductContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\Includes\Contents\Types\Partials\ProductItemTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\ImageTransformer;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    public function transform(ProductContract $product)
    {
        return [
            'title' => $product->getTitle(),
            'description' => $product->getDescription(),
            'image' => $this->transformImage($product),
            'price' => $product->getPrice(),
            'winner' => $product->getWinner(),
            'best_buy' => $product->getBestBuy(),
            'max_points' => $product->getMaxPoints(),
            'items' => $this->transformItems($product)
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
}
