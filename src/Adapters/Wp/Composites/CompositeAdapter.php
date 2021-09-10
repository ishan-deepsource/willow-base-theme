<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Composites;

use Bonnier\Willow\Base\Adapters\Wp\AbstractWpAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\StoryAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\ContentAudioAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\Contents\Types\ContentImageAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\AuthorAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\CommercialAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\GuideMetaAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Categories\CategoryAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Tags\TagAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Vocabulary\VocabularyAdapter;
use Bonnier\Willow\Base\Factories\CompositeContentFactory;
use Bonnier\Willow\Base\Factories\Contracts\ModelFactoryContract;
use Bonnier\Willow\Base\Models\ACF\Composite\CompositeFieldGroup;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Calculator;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\ChaptersSummary;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Inventory;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Multimedia;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Newsletter;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Product;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\Recipe;
use Bonnier\Willow\Base\Models\Base\Root\GuideMeta;
use Bonnier\Willow\Base\Models\Base\Root\Translation;
use Bonnier\Willow\Base\Models\Contracts\Root\GuideMetaContract;
use Bonnier\Willow\Base\Models\WpTaxonomy;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Story;
use Bonnier\Willow\Base\Models\Base\Composites\Contents\Types\AssociatedComposites;
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
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\StoryContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentFileContract;
use Bonnier\Willow\Base\Models\Contracts\Composites\Contents\Types\ContentImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\AudioContract;
use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use Bonnier\Willow\Base\Models\Contracts\Root\CommercialContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;
use Bonnier\Willow\Base\Models\Contracts\Terms\CategoryContract;
use Bonnier\Willow\Base\Services\SiteManagerService;
use Bonnier\Willow\Base\Traits\DateTimeZoneTrait;
use Bonnier\Willow\Base\Traits\UrlTrait;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\Cxense\Parsers\Document;
use Bonnier\WP\Cxense\Services\WidgetDocumentQuery;
use Bonnier\WP\Cxense\WpCxense;
use Bonnier\WP\SiteManager\Repositories\VocabularyRepository;
use Bonnier\WP\SiteManager\Services\VocabularyService;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
        'file'                  => ContentFile::class,
        'gallery'               => Gallery::class,
        'image'                 => ContentImage::class,
        'infobox'               => InfoBox::class,
        'inserted_code'         => InsertedCode::class,
        'link'                  => Link::class,
        'text_item'             => TextItem::class,
        'video'                 => Video::class,
        'audio'                 => ContentAudio::class,
        'quote'                 => Quote::class,
        'associated_composites' => AssociatedComposites::class,
        'paragraph_list'        => ParagraphList::class,
        'hotspot_image'         => HotspotImage::class,
        'lead_paragraph'        => LeadParagraph::class,
        'newsletter'            => Newsletter::class,
        'chapters_summary'      => ChaptersSummary::class,
        'multimedia'            => Multimedia::class,
        'inventory'             => Inventory::class,
        'product'               => Product::class,
        'recipe'                => Recipe::class,
        'calculator'            => Calculator::class,
    ];

    protected $acfFields;

    public function __construct($wpModel)
    {
        parent::__construct($wpModel);
        if ($postId = data_get($this->wpModel, 'ID')) {
            $this->acfFields = WpModelRepository::instance()->getAcfData($postId);
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
        $excludePlatforms = data_get($this->wpModel, 'exclude_platforms');
        return ((isset(collect($excludePlatforms)[0]) && collect($excludePlatforms)[0] ==='web')?'pending':data_get($this->wpModel, 'post_status')) ?: null;
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
            if ($this->getKind() === CompositeFieldGroup::SHELL_VALUE) {
                return $this->getShellLink();
            }
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
            $updatedAt = $this->toDateTime($date);
            $publishedAt = $this->getPublishedAt();
            if ($publishedAt && $publishedAt > $updatedAt) {
                return $publishedAt;
            }
            return $updatedAt;
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

    public function getOtherAuthors(): Collection
    {
        return collect(array_get($this->acfFields, 'other_authors', []))->map(function ($author) {
            $user = get_user_by('id', $author['ID']);
            if ($user instanceof \WP_User) {
                return new Author(new AuthorAdapter($user));
            }
            return  null;
        })->reject(function ($author) {
            return is_null($author);
        });
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
            return $this->stripApi($url);
        }

        return $this->getFullUrl(get_permalink($this->getId()));
    }

    public function getTemplate(): ?string
    {
        return get_page_template_slug($this->getId());
    }

    public function getEstimatedReadingTime(): ?int
    {
        return intval(array_get($this->wpMeta, 'reading_time.0')) ?: 0;
    }

    public function getStory(): ?StoryContract
    {
        if (($storyCompositeId = intval(array_get($this->wpMeta, 'story_parent.0'))) &&
            $storyComposite = WpModelRepository::instance()->getPost($storyCompositeId)
        ) {
            return new Story(new StoryAdapter($storyComposite));
        }
        return null;
    }

    public function getAudio(): ?AudioContract
    {
        if (($audio = WpModelRepository::instance()->getAcfField($this->getId(), 'audio')) &&
            $file = array_get($audio, 'file')
        ) {
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
        return intval(array_get($this->wpMeta, 'word_count.0')) ?: null;
    }

    public function getTranslations(): ?Collection
    {
        $postTranslations = LanguageProvider::getPostTranslations($this->getId());
        $translations = collect($postTranslations)->mapWithKeys(function (int $compositeId, string $locale) {
            if ($compositeId === $this->getId()) {
                $composite = $this->wpModel;
            } else {
                $composite = WpModelRepository::instance()->getPost($compositeId);
            }
            if ($composite instanceof \WP_Post && $composite->post_status === 'publish') {
                return [$locale => new Translation(new CompositeTranslationAdapter($composite))];
            }
            return [$locale => null];
        })->reject(function ($translation) {
            return is_null($translation);
        });

        if ($translations->isNotEmpty()) {
            return $translations;
        }

        return null;
    }

    public function getExcludePlatforms(): ?Collection
    {
        if ($excludePlatforms = data_get($this->wpModel, 'exclude_platforms')) {
            return collect($excludePlatforms);
        }
        return null;
    }

    public function getCtmDisabled(): bool
    {
        return data_get($this->wpModel, 'disable_ctm', false);
    }

    public function getShellLink(): ?string
    {
        return data_get($this->wpModel, 'shell_link');
    }

    public function getRelatedByCategoryQuery(): WidgetDocumentQuery
    {
        return WidgetDocumentQuery::make()
            ->addContext('url', $this->getFullUrl($this->getLink()))
            ->byRelated()
            ->addParameter('pageType', 'article gallery story')
            ->setCategories();
    }

    public function getRelatedByCategory(WidgetDocumentQuery $manualQuery = null): ?Collection
    {
        // Cache is handled inside cxense plugin
        $query = $manualQuery ?: $this->getRelatedByCategoryQuery();


        return collect($query->get()['matches'])->map(
            function (Document $cxArticle) {
                $locale = WpCxense::instance()
                        ->settings
                        ->getOrganisationPrefix(LanguageProvider::getCurrentLanguage('locale')) ?? 'da';
                if ($this->getCommercial() && $cxArticle->{$locale. '-commercial-label'}) {
                    return null;
                }
                $postId = intval($cxArticle->{'recs-articleid'});
                $post = WpModelRepository::instance()->getPost($postId);
                if ($post && $post->post_status === 'publish' && $post->ID === $postId) {
                    return new Composite(new CompositeAdapter($post));
                }
                return null;
            }
        )->reject(function ($content) {
            return is_null($content);
        });
    }

    public function getGuideMeta(): ?GuideMetaContract
    {
        return new GuideMeta(new GuideMetaAdapter($this->acfFields));
    }

    public function getContenthubId(): ?string
    {
        return array_get($this->wpMeta, 'contenthub_id.0') ?: null;
    }

    public function getEditorialType(): ?string
    {
        if (!get_field('editorial_type')) {
            return null;
        }

        $vocabularies = get_the_terms($this->getId(), 'editorial_type');
        if ($vocabularies) {
            return $vocabularies[0]->name;
        }
        return null;
    }

    public function getHideInSitemaps(): ?bool
    {
        $get = array_get($this->acfFields, 'sitemap', false);
        if (!is_bool($get)) {
            return false;
        }
        return $get;
    }
}
