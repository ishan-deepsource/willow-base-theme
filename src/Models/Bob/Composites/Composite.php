<?php

namespace Bonnier\Willow\Base\Models\Bob\Composites;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\StoryContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\AudioContract;
use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use Bonnier\Willow\Base\Models\Contracts\Root\CommercialContract;
use Bonnier\Willow\Base\Models\Contracts\Root\GuideMetaContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;
use Bonnier\Willow\Base\Models\Contracts\Terms\CategoryContract;
use Bonnier\WP\Cxense\Services\WidgetDocumentQuery;
use DateTime;
use Illuminate\Support\Collection;

/**
 * Class Composite
 *
 * @package \Bonnier\Willow\Bob\Models
 */
class Composite implements CompositeContract
{
    protected $composite;

    /**
     * Composite constructor.
     *
     * @param CompositeContract $composite
     */
    public function __construct(CompositeContract $composite)
    {
        $this->composite = $composite;
    }

    public function getId(): int
    {
        return $this->composite->getId();
    }

    public function getKind(): ?string
    {
        return $this->composite->getKind();
    }

    public function getTitle(): ?string
    {
        return $this->composite->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->composite->getDescription();
    }

    public function getContents(): ?Collection
    {
        return $this->composite->getContents();
    }

    public function getLeadImage(): ?ContentImageContract
    {
        return $this->composite->getLeadImage();
    }

    public function getLink(): ?string
    {
        return $this->composite->getLink();
    }

    public function getLabel(): ?string
    {
        return $this->composite->getLabel();
    }

    public function getLabelLink(): ?string
    {
        return $this->composite->getLabelLink();
    }

    public function getFirstInlineImage(): ?ContentImageContract
    {
        return $this->composite->getFirstInlineImage();
    }

    public function getFirstFileImage(): ?ContentImageContract
    {
        return $this->composite->getFirstFileImage();
    }

    public function getPublishedAt(): ?DateTime
    {
        return $this->composite->getPublishedAt();
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->composite->getUpdatedAt();
    }

    public function getStatus(): ?string
    {
        return $this->composite->getStatus();
    }

    public function getLocale(): ?string
    {
        return $this->composite->getLocale();
    }

    public function getAuthor(): ?AuthorContract
    {
        return $this->composite->getAuthor();
    }

    public function getAuthorDescription(): ?string
    {
        return $this->composite->getAuthorDescription();
    }

    public function getOtherAuthors(): ?Collection
    {
        return $this->composite->getOtherAuthors();
    }

    public function getCategory(): ?CategoryContract
    {
        return $this->composite->getCategory();
    }

    public function getVocabularies(): ?Collection
    {
        return $this->composite->getVocabularies();
    }

    public function getCommercial(): ?CommercialContract
    {
        return $this->composite->getCommercial();
    }

    public function getTeaser(string $type): ?TeaserContract
    {
        return $this->composite->getTeaser($type);
    }

    public function getTeasers(): ?Collection
    {
        return $this->composite->getTeasers();
    }

    public function getTags(): Collection
    {
        return $this->composite->getTags();
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->composite->getCanonicalUrl();
    }

    public function getTemplate(): ?string
    {
        return $this->composite->getTemplate();
    }

    public function getEstimatedReadingTime(): ?int
    {
        return $this->composite->getEstimatedReadingTime();
    }

    public function getStory(): ?StoryContract
    {
        return $this->composite->getStory();
    }

    public function getParent(): ?int
    {
        return $this->composite->getParent();
    }

    public function getAudio(): ?AudioContract
    {
        return $this->composite->getAudio();
    }

    public function getWordCount(): ?int
    {
        return $this->composite->getWordCount();
    }

    public function getTranslations(): ?Collection
    {
        return $this->composite->getTranslations();
    }

    public function getExcludePlatforms(): ?Collection
    {
        return $this->composite->getExcludePlatforms();
    }

    public function getCtmDisabled(): bool
    {
        return $this->composite->getCtmDisabled();
    }

    public function getShellLink(): ?string
    {
        return $this->composite->getShellLink();
    }


    public function getRelatedByCategoryQuery(): ?WidgetDocumentQuery
    {
        return $this->composite->getRelatedByCategoryQuery()->setMatchingMode(WidgetDocumentQuery::RELATED_MAX_2_YEARS);
    }

    public function getRelatedByCategory(WidgetDocumentQuery $manualQuery = null): ?Collection
    {
        return $this->composite->getRelatedByCategory($this->getRelatedByCategoryQuery());
    }

    public function getGuideMeta(): ?GuideMetaContract
    {
        // TODO: Implement getGuideMeta() method.
        return null;
    }

    public function getContenthubId(): ?string
    {
        return $this->composite->getContenthubId();
    }

    public function getEditorialType(): ?string
    {
        return $this->composite->getEditorialType();
    }
}
