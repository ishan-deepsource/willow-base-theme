<?php

namespace Bonnier\Willow\Base\Models\Contracts\Root;

use DateTime;
use Illuminate\Support\Collection;

interface AuthorContract
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function getTitle(): ?string;

    public function getBiography(): ?string;

    public function getEducation(): ?string;

    public function getAvatar(): ?ImageContract;

    public function getEmail(): ?string;

    public function getUrl(): ?string;

    public function getWebsite(): ?string;

    public function getBirthday(): ?DateTime;

    public function getContentTeasers($page, $perPage, $orderBy, $order, $offset): Collection;

    public function isPublic(): bool;

    public function isAuthor(): bool;

    public function getCount(): int;
}
