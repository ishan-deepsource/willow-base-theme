<?php

namespace Bonnier\Willow\Base\Models\Gds;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\StoryContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\AudioContract;
use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use Bonnier\Willow\Base\Models\Contracts\Root\CommercialContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;
use Bonnier\Willow\Base\Models\Contracts\Terms\CategoryContract;
use DateTime;
use Illuminate\Support\Collection;

/**
 * Class Composite
 *
 * @package \Bonnier\Willow\Base\Models
 */
class Composite implements CompositeContract
{
    protected $baseComposite;

    /**
     * Composite constructor.
     *
     * @param $post
     */
    public function __construct(CompositeContract $post)
    {
        $this->baseComposite = $post;
    }

    public function getId(): int
    {
        return $this->baseComposite->getId();
    }

    public function getKind(): ?string
    {
        return $this->baseComposite->getKind();
    }

    public function getTitle(): ?string
    {
        return $this->baseComposite->getTitle();
    }

    public function getDescription(): ?string
    {
        return $this->baseComposite->getDescription();
    }

    public function getStatus(): ?string
    {
        return $this->baseComposite->getStatus();
    }

    public function getAuthor(): ?AuthorContract
    {
        return $this->baseComposite->getAuthor();
    }

    public function getAuthorDescription(): ?string
    {
        return $this->baseComposite->getAuthorDescription();
    }

    public function getContents(): ?Collection
    {
        return $this->baseComposite->getContents();
    }

    public function getCategory(): ?CategoryContract
    {
        return $this->baseComposite->getCategory();
    }

    public function getVocabularies(): ?Collection
    {
        return $this->baseComposite->getVocabularies();
    }

    public function getLeadImage(): ?ContentImageContract
    {
        return $this->baseComposite->getLeadImage();
    }

    public function getFirstInlineImage(): ?ContentImageContract
    {
        return $this->baseComposite->getFirstInlineImage();
    }

    public function getFirstFileImage(): ?ContentImageContract
    {
        return $this->baseComposite->getFirstFileImage();
    }

    public function getLink(): ?string
    {
        return $this->baseComposite->getLink();
    }

    public function getCommercial(): ?CommercialContract
    {
        return $this->baseComposite->getCommercial();
    }

    public function getLabel(): ?string
    {
        return $this->baseComposite->getLabel();
    }

    public function getLabelLink(): ?string
    {
        return $this->baseComposite->getLabelLink();
    }

    public function getPublishedAt(): ?DateTime
    {
        return $this->baseComposite->getPublishedAt();
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->baseComposite->getUpdatedAt();
    }

    public function getTeaser(string $type): ?TeaserContract
    {
        return $this->baseComposite->getTeaser($type);
    }

    public function getTeasers(): ?Collection
    {
        return $this->baseComposite->getTeasers();
    }

    public function getLocale(): ?string
    {
        return $this->baseComposite->getLocale();
    }

    public function getTags(): Collection
    {
        return null;
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->baseComposite->getCanonicalUrl();
    }

    public function getTemplate(): ?string
    {
        return $this->baseComposite->getTemplate();
    }

    public function getEstimatedReadingTime(): ?int
    {
        return $this->baseComposite->getEstimatedReadingTime();
    }

    public function getStory(): ?StoryContract
    {
        return $this->baseComposite->getStory();
    }

    public function getParent(): ?int
    {
        return $this->baseComposite->getParent();
    }

    public function getAudio(): ?AudioContract
    {
        return $this->baseComposite->getAudio();
    }

    public function getWordCount(): ?int
    {
        return $this->baseComposite->getWordCount();
    }

    public function getLanguageUrls(): ?Collection
    {
        return $this->baseComposite->getLanguageUrls();
    }

    public function getExcludePlatforms(): ?Collection
    {
        return $this->baseComposite->getExcludePlatforms();
    }
}
