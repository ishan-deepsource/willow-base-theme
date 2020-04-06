<?php

namespace Bonnier\Willow\Base\Models\Contracts\Terms;

use Illuminate\Support\Collection;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;

interface TagContract
{
    public function getId(): ?int;

    public function getName(): ?string;

    public function getSlug(): ?string;

    public function getUrl(): ?string;

    public function getLanguage(): ?string;

    public function getContentTeasers($page, $perPage, $orderBy, $order): Collection;

    public function getCount(): ?int;

    public function getCanonicalUrl(): ?string;

    public function getTeaser(string $type): ?TeaserContract;

    public function getTeasers(): ?Collection;

    public function getContents(int $page): ?Collection;

    public function getTranslations(): ?Collection;

    public function getContenthubId(): ?string;
}
