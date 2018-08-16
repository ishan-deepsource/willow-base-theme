<?php

namespace Bonnier\Willow\Base\Adapters\Cxense\Search;

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

    public function __construct(Document $document)
    {
        $this->document = $document;
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

    public function getContents(): ?Collection
    {
        return null;
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
        return $this->document->getField('url');
    }

    public function getCommercial(): ?CommercialContract
    {
        return !empty($this->document->getField('bod-commercial-label')) ?
                new Commercial(new CommercialAdapter($this->document)) :
                null;
    }

    public function getLabel(): ?string
    {
        return $this->getCommercial() ? $this->getCommercial()->getLabel() : $this->getCategory()->getName();
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
        return collect([]);
    }

    public function getCanonicalUrl(): ?string
    {
        return null;
    }

    public function getTemplate(): ?string
    {
        return null;
    }
}
