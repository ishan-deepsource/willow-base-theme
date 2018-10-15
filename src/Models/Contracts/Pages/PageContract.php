<?php

namespace Bonnier\Willow\Base\Models\Contracts\Pages;

use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;
use DateTime;
use Illuminate\Support\Collection;

interface PageContract
{
    public function getId(): int;

    public function getTitle(): ?string;

    public function getContent(): ?string;

    public function getStatus(): ?string;

    public function getAuthor(): ?AuthorContract;

    public function getTemplate(): ?string;

    public function getPublishedAt(): ?DateTime;

    public function getUpdatedAt(): ?DateTime;

    public function isFrontPage(): bool;

    public function getTeaser(string $type): ?TeaserContract;

    public function getTeasers(): ?Collection;

    public function getCanonicalUrl(): ?string;

    public function getContents(): ?Collection;
}
