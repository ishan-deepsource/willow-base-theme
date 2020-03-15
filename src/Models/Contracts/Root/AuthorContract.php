<?php

namespace Bonnier\Willow\Base\Models\Contracts\Root;

use Illuminate\Support\Collection;

interface AuthorContract
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function getTitle(): ?string;

    public function getBiography(): ?string;

    public function getAvatar(): ?ImageContract;

    public function getUrl(): ?string;

    public function getContentTeasers($page, $perPage, $orderBy, $order, $offset): Collection;
}
