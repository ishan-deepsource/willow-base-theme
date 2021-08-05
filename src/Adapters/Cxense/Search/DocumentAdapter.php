<?php

namespace Bonnier\Willow\Base\Adapters\Cxense\Search;

use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\StoryContract;
use Bonnier\Willow\Base\Models\Contracts\Root\AudioContract;
use Bonnier\WP\Cxense\Parsers\Document;
use Bonnier\Willow\Base\Adapters\Cxense\Search\Partials\DocumentTeaserAdapter;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\ContentImage;
use Bonnier\Willow\Base\Models\Base\Root\Author;
use Bonnier\Willow\Base\Models\Base\Root\Commercial;
use Bonnier\Willow\Base\Models\Base\Root\Teaser;
use Bonnier\Willow\Base\Models\Base\Terms\Category;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use Bonnier\Willow\Base\Models\Contracts\Root\CommercialContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;
use Bonnier\Willow\Base\Models\Contracts\Terms\CategoryContract;
use Bonnier\Willow\Base\Traits\DateTimeZoneTrait;
use Bonnier\WP\Cxense\Services\WidgetDocumentQuery;
use Bonnier\WP\Cxense\WpCxense;
use DateTime;
use Illuminate\Support\Collection;

/**
 * Class DocumentAdapter
 *
 * @package \\${NAMESPACE}
 */
class DocumentAdapter implements CompositeContract
{
    use DateTimeZoneTrait;

    protected $document;
    protected $orgPreFix;

    public function __construct(Document $document)
    {
        $this->document = $document;
        $this->orgPreFix = WpCxense::instance()->settings->getOrganisationPrefix();
    }

    public function getId(): int
    {
        return $this->document->getField('recs-articleid') ?? 0;
    }

    public function getTitle(): ?string
    {
        return $this->document->getField('title');
    }

    public function getDescription(): ?string
    {
        return
            $this->document->getHighlight('description') ??
            $this->document->getField('description') ??
            null;
    }

    public function getStatus(): ?string
    {
        return null;
    }

    public function getAuthor(): ?AuthorContract
    {
        return new Author(new AuthorAdapter($this->document));
    }

    public function getAuthorDescription(): ?string
    {
        return null;
    }

    public function getOtherAuthors(): ?Collection
    {
        return null;
    }

    public function getContents(): ?Collection
    {
        $arr = [[ 'type' => 'cxense' ]];
        $this->addFieldNameValuesToArray($arr, [
            $this->orgPreFix . '-recipe-meta-energy',
            $this->orgPreFix . '-recipe-meta-energy-unit',
            $this->orgPreFix . '-recipe-meta-time',
            $this->orgPreFix . '-recipe-meta-time-unit',
            $this->orgPreFix . '-video-meta-duration',
            $this->orgPreFix . '-video-meta-workout-time',
            $this->orgPreFix . '-video-meta-workout-level',
        ]);
        return collect($arr);
    }

    public function getCategory(): ?CategoryContract
    {
        return new Category(new CategoryAdapter($this->document));
    }

    public function getLeadImage(): ?ContentImageContract
    {
        return new ContentImage(new ContentImageAdapter($this->document));
    }

    public function getFirstInlineImage(): ?ContentImageContract
    {
        return new ContentImage(new ContentImageAdapter($this->document));
    }

    public function getFirstFileImage(): ?ContentImageContract
    {
        return new ContentImage(new ContentImageAdapter($this->document));
    }

    public function getLink(): ?string
    {
        return parse_url($this->document->getField('url'), PHP_URL_PATH);
    }

    public function getCommercial(): ?CommercialContract
    {
        return !empty($this->document->getField($this->orgPreFix . '-commercial-label')) ?
            new Commercial(new CommercialAdapter($this->document)) :
            null;
    }

    public function getLabel(): ?string
    {
        return $this->getCommercial()
            && !empty($this->getCommercial()->getLabel())
            && $this->getCommercial()->getLabel() != 'editorial'
                ? $this->getCommercial()->getLabel()
                : $this->getCategory()->getName();
    }

    public function getLabelLink(): ?string
    {
        return $this->getCommercial() ? null : $this->getCategory()->getUrl();
    }

    public function getPublishedAt(): ?DateTime
    {
        if ($publishedTime = $this->document->getField('recs-publishtime')) {
            return $this->toDateTime($publishedTime);
        }

        return null;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return null;
    }

    public function getTeaser(string $type): ?TeaserContract
    {
        return new Teaser(new DocumentTeaserAdapter($this, $type));
    }

    public function getTeasers(): ?Collection
    {
        return collect($this->getTeaser('default'));
    }

    public function getLocale(): ?string
    {
        // todo find the right field
        return null;
    }

    public function getTags(): Collection
    {
        return collect([]);
    }

    public function getVocabularies(): Collection
    {
        // todo find the right field
        return collect([]);
    }

    public function getCanonicalUrl(): ?string
    {
        // todo find the right field
        return null;
    }

    public function getHideInSitemaps(): ?bool
    {
        // todo find the right field
        return null;
    }

    public function getTemplate(): ?string
    {
        return $this->document->getField($this->orgPreFix . '-template');
    }

    public function getEstimatedReadingTime(): ?int
    {
        // todo find the right field
        return null;
    }

    public function getKind(): ?string
    {
        // todo find the right field
        return null;
    }

    public function getParent(): ?int
    {
        // todo find the right field
        return null;
    }

    public function getStory(): ?StoryContract
    {
        // todo find the right field
        return null;
    }

    public function getAudio(): ?AudioContract
    {
        // todo find the right field
        return null;
    }

    public function getWordCount(): ?int
    {
        // todo find the right field
        return null;
    }

    public function getTranslations(): ?Collection
    {
        return null;
    }

    public function getExcludePlatforms(): ?Collection
    {
        return null;
    }

    public function getCtmDisabled(): bool
    {
        return false;
    }

    public function getShellLink(): ?string
    {
        return null;
    }

    public function getRelatedByCategory(WidgetDocumentQuery $manualQuery = null): ?Collection
    {
        return null;
    }

    public function getRelatedByCategoryQuery(): ?WidgetDocumentQuery
    {
        return null;
    }

    public function getContenthubId(): ?string
    {
        return null;
    }
    public function getEditorialType(): ?string
    {
        return null;
    }


    private function addFieldNameValuesToArray(array &$arr, array $fieldNames): void
    {
        foreach ($fieldNames as $fieldNameIndex => $fieldNameValue) {
            $value = $this->document->getField($fieldNameValue);
            if ($value !== null) {
                $arr[0][$fieldNameValue] = $value;
            }
        }
    }
}
