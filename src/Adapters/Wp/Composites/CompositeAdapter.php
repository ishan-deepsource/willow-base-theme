<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites;

use Bonnier\Willow\Base\Adapters\Wp\AbstractWpAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\AssociatedContentAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\ContentAudioAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\ContentImageAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\AuthorAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\CommercialAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Categories\CategoryAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Tags\TagAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Vocabulary\VocabularyAdapter;
use Bonnier\Willow\Base\Factories\CompositeContentFactory;
use Bonnier\Willow\Base\Factories\Contracts\ModelFactoryContract;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\AssociatedContent;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\ContentAudio;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\ContentFile;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\ContentImage;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Gallery;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\HotspotImage;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\InfoBox;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\InsertedCode;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\LeadParagraph;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Link;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\ParagraphList;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Quote;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\TextItem;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Video;
use Bonnier\Willow\Base\Models\Base\Root\Author;
use Bonnier\Willow\Base\Models\Base\Root\Commercial;
use Bonnier\Willow\Base\Models\Base\Root\Teaser;
use Bonnier\Willow\Base\Models\Base\Terms\Category;
use Bonnier\Willow\Base\Models\Base\Terms\Tag;
use Bonnier\Willow\Base\Models\Base\Terms\Vocabulary;
use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\ContentContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentFileContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\AudioContract;
use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use Bonnier\Willow\Base\Models\Contracts\Root\CommercialContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;
use Bonnier\Willow\Base\Models\Contracts\Terms\CategoryContract;
use Bonnier\Willow\Base\Traits\DateTimeZoneTrait;
use Bonnier\Willow\Base\Traits\UrlTrait;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\ContentHub\Editor\Models\WpTaxonomy;
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
        'file'                 => ContentFile::class,
        'gallery'              => Gallery::class,
        'image'                => ContentImage::class,
        'infobox'              => InfoBox::class,
        'inserted_code'        => InsertedCode::class,
        'link'                 => Link::class,
        'text_item'            => TextItem::class,
        'video'                => Video::class,
        'audio'                => ContentAudio::class,
        'quote'                => Quote::class,
        'associated_composite' => AssociatedContent::class,
        'paragraph_list'       => ParagraphList::class,
        'hotspot_image'        => HotspotImage::class,
        'lead_paragraph'       => LeadParagraph::class,
    ];

    protected $acfFields;

    public function __construct($wpModel)
    {
        parent::__construct($wpModel);
        if ($postId = data_get($this->wpModel, 'ID')) {
            $this->acfFields = get_fields($postId);
        }
        $this->compositeContents = array_get($this->acfFields, 'composite_content') ?: null;
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
        return data_get($this->wpModel, 'ID', 0);
    }

    public function getKind(): ?string
    {
        return array_get($this->acfFields, 'kind', 'Article');
    }

    public function getParent(): ?int
    {
        return data_get($this->wpModel, 'post_parent');
    }

    public function getTitle(): ?string
    {
        return data_get($this->wpModel, 'post_title') ?: null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->acfFields, 'description') ?: null;
    }

    public function getStatus(): ?string
    {
        return data_get($this->wpModel, 'post_status') ?: null;
    }

    public function getLocale(): ?string
    {
        return LanguageProvider::getPostLanguage($this->getId());
    }

    public function getContents(): ?Collection
    {
        if (! $this->contents) {
            $this->contents = collect($this->compositeContents)->map(function ($acfContentArray) {
                if (array_get($acfContentArray, 'lead_image')) {
                    return null;
                }
                $class = collect($this->contentModelsMapping)->get(array_get($acfContentArray, 'acf_fc_layout'));
                try {
                    return $this->getContentFactory($class)->getModel($acfContentArray);
                } catch (\InvalidArgumentException $exception) {
                    return null;
                }
            })->reject(function ($content) {
                return is_null($content);
            });
        }

        return $this->contents;
    }

    public function getLeadImage(): ?ContentImageContract
    {
        $leadImageContent = collect($this->compositeContents)->first(function ($acfContentArray) {
            return array_get($acfContentArray, 'lead_image', false);
        });
        try {
            if ($leadImageContent) {
                return new ContentImage(new ContentImageAdapter($leadImageContent));
            }
        } catch (\InvalidArgumentException $exception) {
        }
        return null;
    }

    public function getFirstInlineImage(): ?ContentImageContract
    {
        return $this->getContents()->first(function (ContentContract $content) {
                return $content instanceof ContentImageContract;
        }) ?? null;
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
        }, null);
    }

    public function getLink(): ?string
    {
        if ($postId = $this->getId()) {
            return get_permalink($postId);
        }

        return null;
    }

    public function getLabel(): ?string
    {
        return $this->getCommercial() ?
            optional($this->getCommercial())->getLabel() :
            optional($this->getCategory())->getName();
    }

    public function getLabelLink(): ?string
    {
        if (! $this->getCommercial()) {
            return optional($this->getCategory())->getUrl();
        }
        return null;
    }

    public function getPublishedAt(): ?DateTime
    {
        if ($date = data_get($this->wpModel, 'post_date')) {
            return $this->toDateTime($date);
        }
        return null;
    }

    public function getUpdatedAt(): ?DateTime
    {
        if ($date = data_get($this->wpModel, 'post_modified')) {
            return $this->toDateTime($date);
        }
        return null;
    }

    public function getAuthor(): ?AuthorContract
    {
        if (($author = data_get($this->wpModel, 'post_author')) && $wpUser = get_user_by('id', $author)) {
            return new Author(new AuthorAdapter($wpUser));
        }

        return null;
    }

    public function getAuthorDescription(): ?string
    {
        return array_get($this->acfFields, 'author_description') ?: null;
    }

    public function getCategory(): ?CategoryContract
    {
        if ($category = array_get($this->acfFields, 'category')) {
            return new Category(new CategoryAdapter($category));
        }

        return null;
    }

    public function getVocabularies(): ?Collection
    {
        return collect(WpTaxonomy::get_custom_taxonomies())->map(function ($taxonomy) {
            return new Vocabulary(new VocabularyAdapter($this, $taxonomy));
        });
    }

    public function getTags(): Collection
    {
        return collect(array_get($this->acfFields, 'tags', []))->map(function ($tag) {
            if ($tag instanceof \WP_Term) {
                return new Tag(new TagAdapter($tag));
            }
            return null;
        })->reject(function ($tag) {
            return is_null($tag);
        });
    }

    public function getCommercial(): ?CommercialContract
    {
        $commercial = new Commercial(new CommercialAdapter($this->acfFields));
        return $commercial->getType() ? $commercial : null;
    }

    private function getContentFactory($class): ModelFactoryContract
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
        return intval(get_post_meta($this->getId(), 'reading_time', true)) ?: 0;
    }

    public function getAssociatedComposites(): ?Collection
    {
        $associatedComposites = get_field('composite_content', $this->getParent());
        if (! $associatedComposites) {
            return null;
        }

        return collect($associatedComposites)->map(function ($acfContentArray) {
            if (array_get($acfContentArray, 'acf_fc_layout') === 'associated_composite') {
                return new AssociatedContent(new AssociatedContentAdapter($acfContentArray));
            }
            return null;
        })->reject(function ($associatedContent) {
            return is_null($associatedContent);
        });
    }

    public function getAudio(): ?AudioContract
    {
        if (($audio = get_field('audio')) && $file = array_get($audio, 'file')) {
            return new ContentAudioAdapter([
                'title' => array_get($audio, 'title'),
                'file'  => $file,
                'image' => array_get($audio, 'audio_thumbnail'),
            ]);
        }
        return null;
    }

    public function getWordCount(): ?int
    {
        return intval(get_post_meta($this->getId(), 'word_count', true)) ?: null;
    }

    public function getLanguageUrls(): ?Collection
    {
        return collect(LanguageProvider::getPostTranslations($this->getId()))->map(function ($compositeId) {
            return get_permalink($compositeId);
        });
    }
}
