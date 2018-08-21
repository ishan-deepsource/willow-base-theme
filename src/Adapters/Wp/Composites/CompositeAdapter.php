<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites;

use Bonnier\Willow\Base\Adapters\Wp\AbstractWpAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\ContentImageAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\AudioAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\AuthorAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\CommercialAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Categories\CategoryAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Tags\TagAdapter;
use Bonnier\Willow\Base\Factories\CompositeContentFactory;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\ContentAudio;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\ContentFile;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\ContentImage;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Gallery;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\InfoBox;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\InsertedCode;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Link;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\TextItem;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Video;
use \Bonnier\Willow\Base\Models\Base\Root\Audio;
use Bonnier\Willow\Base\Models\Base\Root\Author;
use Bonnier\Willow\Base\Models\Base\Root\Commercial;
use Bonnier\Willow\Base\Models\Base\Root\Teaser;
use Bonnier\Willow\Base\Models\Base\Terms\Category;
use Bonnier\Willow\Base\Models\Base\Terms\Tag;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentFileContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use Bonnier\Willow\Base\Models\Contracts\Root\CommercialContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;
use Bonnier\Willow\Base\Models\Contracts\Terms\CategoryContract;
use Bonnier\Willow\Base\Traits\DateTimeZoneTrait;
use Bonnier\Willow\Base\Traits\UrlTrait;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use DateTime;
use Illuminate\Support\Collection;

/**
 * Class CompositeAdapter
 *
 * @property \WP_Post $wpModel
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class CompositeAdapter extends AbstractWpAdapter implements CompositeContract
{
    use DateTimeZoneTrait, UrlTrait;

    /** @var CompositeContentFactory */
    protected $contentFactory;
    protected $compositeContents;
    protected $contents;

    protected $contentModelsMapping = [
        'file'          => ContentFile::class,
        'gallery'       => Gallery::class,
        'image'         => ContentImage::class,
        'infobox'       => InfoBox::class,
        'inserted_code' => InsertedCode::class,
        'link'          => Link::class,
        'text_item'     => TextItem::class,
        'video'         => Video::class,
        'audio'         => ContentAudio::class,
    ];

    protected $acfFields;

    public function __construct($wpModel)
    {
        parent::__construct($wpModel);
        if ($this->wpModel) {
            $this->acfFields = get_fields($this->wpModel->ID);
        }
        $this->compositeContents = $this->acfFields['composite_content'] ?? [];
    }

    public function getAcfFields()
    {
        return $this->acfFields;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->wpModel->ID ?? 0;
    }

    public function getTitle(): ?string
    {
        return $this->wpModel->post_title ?? null;
    }

    public function getDescription(): ?string
    {
        return $this->acfFields['description'] ?? null;
    }

    public function getStatus(): ?string
    {
        return $this->wpModel->post_status ?? null;
    }

    public function getLocale(): ?string
    {
        return LanguageProvider::getPostLanguage($this->getId());
    }

    public function getContents(): ?Collection
    {
        if (!$this->contents) {
            $this->contents = collect($this->compositeContents)->map(function ($acfContentArray) {
                if ($acfContentArray['lead_image'] ?? false) {
                    return null;
                }
                $class = collect($this->contentModelsMapping)->get($acfContentArray['acf_fc_layout']);
                return $this->getContentFactory($class)->getModel($acfContentArray);
            })->reject(function ($content) {
                return is_null($content);
            });
        }

        return $this->contents;
    }

    public function getLeadImage(): ?ContentImageContract
    {
        $leadImageContent = collect($this->compositeContents)->first(function ($acfContentArray) {
            return $acfContentArray['lead_image'] ?? false;
        });
        if ($leadImageContent) {
            return new ContentImage(new ContentImageAdapter($leadImageContent));
        }
        return new ContentImage(new ContentImageAdapter([]));
    }

    public function getFirstInlineImage(): ?ContentImageContract
    {
        return $this->getContents()->first(function (ContentContract $content) {
            return $content instanceof ContentImageContract;
        }) ?? new ContentImage(new ContentImageAdapter([]));
    }

    public function getFirstFileImage(): ?ContentImageContract
    {
        return collect($this->getContents())->reduce(function ($returnVal, ContentContract $content) {
            if ($content instanceof ContentFileContract) {
                $fileImage = $content->getImages()->first();
                if ($fileImage instanceof ContentImageContract) {
                    $returnVal = $fileImage;
                }
            }
            return $returnVal;
        }, new ContentImage(new ContentImageAdapter([])));
    }

    public function getLink(): ?string
    {
        if (!$this->wpModel) {
            return null;
        }
        return get_permalink($this->wpModel->ID);
    }

    public function getLabel(): ?string
    {
        return $this->getCommercial() ?
            optional($this->getCommercial())->getLabel() :
            optional($this->getCategory())->getName();
    }

    public function getLabelLink(): ?string
    {
        if (!$this->getCommercial()) {
            return optional($this->getCategory())->getUrl();
        }
        return null;
    }

    public function getPublishedAt(): ?DateTime
    {
        if (!$this->wpModel) {
            return null;
        }
        return $this->toDateTime($this->wpModel->post_date);
    }

    public function getUpdatedAt(): ?DateTime
    {
        if (!$this->wpModel) {
            return null;
        }
        return $this->toDateTime($this->wpModel->post_modified);
    }

    public function getAuthor(): ?AuthorContract
    {
        if (!$this->wpModel) {
            return null;
        }
        if ($wpUser = get_user_by('id', $this->wpModel->post_author)) {
            return new Author(new AuthorAdapter($wpUser));
        }

        return null;
    }

    public function getAuthorDescription(): ?string
    {
        return $this->acfFields['author_description'] ?? null;
    }

    public function getCategory(): ?CategoryContract
    {
        if ($category = $this->acfFields['category'] ?? null) {
            return new Category(new CategoryAdapter($category));
        }

        return null;
    }

    public function getTags(): Collection
    {
        if (!$this->acfFields['tags']) {
            return collect([]);
        }
        return collect($this->acfFields['tags'])->map(function (\WP_Term $tag) {
            return new Tag(new TagAdapter($tag));
        });
    }

    public function getCommercial(): ?CommercialContract
    {
        $commercial = new Commercial(new CommercialAdapter($this->acfFields));
        return $commercial->getType() ? $commercial : null;
    }

    private function getContentFactory($class)
    {
        if ($this->contentFactory) {
            return $this->contentFactory->setBaseClass($class);
        }

        return $this->contentFactory = new CompositeContentFactory($class);
    }

    public function getTeaser(string $type): ?TeaserContract
    {
        return new Teaser(new CompositeTeaserAdapter($this, $type));
    }

    public function getTeasers(): ?Collection
    {
        return collect([
            $this->getTeaser('default'),
            $this->getTeaser('seo'),
            $this->getTeaser('facebook'),
            $this->getTeaser('twitter'),
        ]);
    }

    public function getCanonicalUrl(): ?string
    {
        if (isset($this->acfFields['canonical_url']) && $url = $this->acfFields['canonical_url']) {
            return $url;
        }

        return $this->getFullUrl(get_permalink($this->getId()));
    }

    public function getTemplate(): ?string
    {
        return get_page_template_slug($this->getId());
    }

    public function getEstimatedReadingTime(): ?int
    {
        return intval(get_post_meta($this->getId(), 'reading_time', true)) ?: null;
    }
}
