<?php

namespace Bonnier\Willow\Base\Models\Contracts\Composites;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use Bonnier\Willow\Base\Models\Contracts\Root\CommercialContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;
use Bonnier\Willow\Base\Models\Contracts\Terms\CategoryContract;
use DateTime;
use Illuminate\Support\Collection;

interface CompositeContract
{
    public function getId(): int;

    public function getTitle(): ?string;

    public function getDescription(): ?string;

    public function getStatus(): ?string;

    public function getLocale(): ?string;

    public function getAuthor(): ?AuthorContract;

    public function getAuthorDescription(): ?string;

    public function getTeaser(string $type): ?TeaserContract;

    public function getTeasers(): ?Collection;

    public function getContents(): ?Collection;

    public function getCategory(): ?CategoryContract;

    public function getVocabularies(): ?Collection;

    public function getTags(): Collection;

    public function getLeadImage(): ?ContentImageContract;

    public function getFirstInlineImage(): ?ContentImageContract;

    public function getFirstFileImage(): ?ContentImageContract;

    public function getLink(): ?string;

    public function getCanonicalUrl(): ?string;

    public function getCommercial(): ?CommercialContract;

    public function getLabel(): ?string;

    public function getLabelLink(): ?string;

    public function getTemplate(): ?string;

    public function getPublishedAt(): ?DateTime;

    public function getUpdatedAt(): ?DateTime;

    public function getEstimatedReadingTime(): ?string;
}
