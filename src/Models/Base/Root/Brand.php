<?php

namespace Bonnier\Willow\Base\Models\Base\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\BrandContract;

/**
 * Class BrandContract
 *
 * @package \Bonnier\Willow\Base\Models\Contracts\Root
 */
class Brand implements BrandContract
{
    protected $brand;

    public function __construct(BrandContract $brand)
    {
        $this->brand = $brand;
    }

    public function getId(): int
    {
        return $this->brand->getId();
    }
    public function getName(): ?string
    {
        return $this->brand->getName();
    }
    public function getBrandCode(): ?string
    {
        return $this->brand->getId();
    }
}
