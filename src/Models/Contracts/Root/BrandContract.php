<?php

namespace Bonnier\Willow\Base\Models\Contracts\Root;

/**
 * Class BrandContract
 *
 * @package \Bonnier\Willow\Base\Models\Contracts\Root
 */
interface BrandContract
{
    public function getId(): int;
    public function getName(): ?string;
    public function getBrandCode(): ?string;
}
