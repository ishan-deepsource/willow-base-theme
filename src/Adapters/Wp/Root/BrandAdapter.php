<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\BrandContract;

/**
 * Class BrandAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp\Root
 */
class BrandAdapter implements BrandContract
{
    protected $brand;

    public function __construct($brand)
    {
        $this->brand = $brand;
    }

    public function getId(): int
    {
        return $this->brand->id;
    }
    public function getName(): ?string
    {
        return $this->brand->name;
    }
    public function getBrandCode(): ?string
    {
        return $this->brand->brand_code;
    }
}
