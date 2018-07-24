<?php

namespace Bonnier\Willow\Base\Models\Contracts\Terms;

use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;
use Illuminate\Support\Collection;

interface CategoryContract
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function getUrl(): ?string;

    public function getChildren(): ?Collection;

    public function getBody(): ?string;

    public function getImage(): ?ImageContract;

    public function getDescription(): ?string;

    public function getLanguage(): ?string;
    
    public function getContentTeasers($page, $perPage, $orderBy, $order, $offset): Collection;

    public function getCount(): ?int;

    public function getTeaser(string $type): ?TeaserContract;

    public function getTeasers(): ?Collection;

    public function getParent(): ?CategoryContract;

    public function getCanonicalUrl(): ?string;
}
