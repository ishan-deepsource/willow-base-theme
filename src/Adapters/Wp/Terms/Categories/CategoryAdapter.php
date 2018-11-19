<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Terms\Categories;

use Bonnier\Willow\Base\Factories\CategoryContentFactory;
use Bonnier\Willow\Base\Models\Base\Root\Translation;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\BannerPlacement;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\FeaturedContent;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\Newsletter;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\SeoText;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\TaxonomyList;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\TeaserList;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\ContentHub\Editor\Helpers\AcfName;
use Bonnier\WP\ContentHub\Editor\Models\WpComposite;
use Bonnier\Willow\Base\Adapters\Wp\AbstractWpAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Terms\Categories\Partials\CategoryImageAdapter;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Base\Root\Teaser;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;
use Bonnier\Willow\Base\Models\Contracts\Terms\CategoryContract;
use Bonnier\Willow\Base\Traits\UrlTrait;
use Bonnier\WP\SiteManager\WpSiteManager;
use Illuminate\Support\Collection;

/**
 * Class CategoryAdapter
 *
 * @property \WP_Term $wpModel
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp\Terms
 */
class CategoryAdapter extends AbstractWpAdapter implements CategoryContract
{
    use UrlTrait;

    protected $meta;
    protected $acfFields;
    protected $categoryContents;
    protected $contents;
    /** @var CategoryContentFactory */
    protected $contentFactory;

    protected $contentModelsMapping = [
        AcfName::WIDGET_TEASER_LIST => TeaserList::class,
        AcfName::WIDGET_FEATURED_CONTENT => FeaturedContent::class,
        AcfName::WIDGET_SEO_TEXT => SeoText::class,
        AcfName::WIDGET_NEWSLETTER => Newsletter::class,
        AcfName::WIDGET_BANNER_PLACEMENT => BannerPlacement::class,
        AcfName::WIDGET_TAXONOMY_TEASER_LIST => TaxonomyList::class,
    ];

    public function __construct(\WP_Term $wpModel)
    {
        parent::__construct($wpModel);
        $this->meta = $this->getMeta();
        $this->acfFields = WpModelRepository::instance()->getAcfData(sprintf(
            '%s_%s',
            $this->wpModel->taxonomy ?? null,
            $this->wpModel->term_id ?? null
        ));
        $this->categoryContents = array_get($this->acfFields, AcfName::GROUP_PAGE_WIDGETS) ?: null;
    }

    public function getId(): ?int
    {
        return data_get($this->wpModel, 'term_id') ?: null;
    }

    public function getName(): ?string
    {
        return data_get($this->wpModel, 'name') ?: null;
    }

    public function getChildren(): ?Collection
    {
        return collect(get_categories('hide_empty=0&parent=' . $this->getId()))->transform(function ($categoryChild) {
            if (($category = get_category($categoryChild)) && $category instanceof \WP_Term) {
                return new self($category);
            }
            return null;
        })->reject(function ($child) {
            return is_null($child);
        });
    }

    public function getTitle(): ?string
    {
        return data_get($this->meta, 'title.' . LanguageProvider::getCurrentLanguage()) ?: null;
    }

    public function getDescription(): ?string
    {
        return data_get($this->meta, 'description.' . LanguageProvider::getCurrentLanguage()) ?: null;
    }

    public function getBody(): ?string
    {
        return data_get($this->meta, 'body.' . LanguageProvider::getCurrentLanguage()) ?: null;
    }

    public function getMetaDescription(): ?string
    {
        return data_get($this->meta, 'meta_description.' . LanguageProvider::getCurrentLanguage()) ?: null;
    }

    public function getImage(): ?ImageContract
    {
        return new Image(new CategoryImageAdapter($this->meta));
    }

    public function getUrl(): ?string
    {
        if ($link = get_term_link($this->getId(), 'category')) {
            return is_wp_error($link) ? null : $link;
        }

        return null;
    }

    public function getLanguage(): ?string
    {
        if ($categoryId = $this->getId()) {
            return LanguageProvider::getTermLanguage($categoryId);
        }

        return null;
    }

    public function getContentTeasers(
        $page = 1,
        $perPage = 10,
        $orderBy = 'date',
        $order = 'DESC',
        $offset = 0
    ): Collection {
        $offset = $offset ?: ($perPage * ($page - 1));
        return collect(get_posts([
            'post_type' => WpComposite::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => $perPage,
            'offset' => $offset,
            'orderby' => $orderBy,
            'order'  => $order,
            'tax_query' => [
                [
                    'taxonomy' => 'category',
                    'field' => 'term_id',
                    'terms' => $this->getId(),
                    'include_children' => false,
                ]
            ]
        ]))->map(function (\WP_Post $post) {
            $composite = WpModelRepository::instance()->getPost($post);
            return new Composite(new CompositeAdapter($composite));
        });
    }

    public function getCount(): ?int
    {
        return data_get($this->wpModel, 'count');
    }

    public function getTeaser(string $type): ?TeaserContract
    {
        return new Teaser(new CategoryTeaserAdapter($this->meta, $type));
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

    private function getMeta()
    {
        if ($contentHubId = $this->getContenthubId()) {
            try {
                $category = WpSiteManager::instance()->categories()->findByContentHubId($contentHubId) ?? null;
                return data_get($category, 'data');
            } catch (\Exception $exception) {
                return null;
            }
        }
        return null;
    }

    public function getParent(): ?CategoryContract
    {
        if (($parentId = intval(data_get($this->wpModel, 'parent'))) && $parent = get_category($parentId)) {
            if ($parent instanceof \WP_Term) {
                return new static($parent);
            }
        }

        return null;
    }

    public function getAncestor(): ?CategoryContract
    {
        if (($ancestor = $this->findAncestor($this->wpModel)) && $ancestor->term_id !== $this->getId()) {
            return new static($ancestor);
        }

        return null;
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->getFullUrl(get_category_link($this->getId()));
    }

    public function getContents(): ?Collection
    {
        if (!$this->contents) {
            $this->contents = collect($this->categoryContents)->map(function ($acfContentArray) {
                $class = collect($this->contentModelsMapping)->get(array_get($acfContentArray, 'acf_fc_layout'));
                return $this->getContentFactory($class)->getModel($acfContentArray);
            })->reject(function ($content) {
                return is_null($content);
            });
        }

        return $this->contents;
    }

    public function getTranslations(): ?Collection
    {
        $termTranslations = LanguageProvider::getTermTranslations($this->getId());
        $translations = collect($termTranslations)->mapWithKeys(function (int $termId, string $locale) {
            if ($termId === $this->getId()) {
                $term = $this->wpModel;
            } else {
                $term = get_term($termId);
            }
            if ($term instanceof \WP_Term) {
                return [$locale => new Translation(new CategoryTranslationAdapter($term))];
            }
            return null;
        })->reject(function ($translation) {
            return is_null($translation);
        });

        if ($translations->isNotEmpty()) {
            return $translations;
        }

        return null;
    }

    public function getContenthubId(): ?string
    {
        return array_get($this->wpMeta, 'content_hub_id.0') ?: null;
    }

    private function getContentFactory($class)
    {
        if ($this->contentFactory) {
            return $this->contentFactory->setBaseClass($class);
        }

        return $this->contentFactory = new CategoryContentFactory($class);
    }

    private function findAncestor(\WP_Term $category): ?\WP_Term
    {
        if (($parentId = data_get($category, 'parent')) && $parent = get_category($parentId)) {
            if ($parent instanceof \WP_Term) {
                return $this->findAncestor($parent);
            }
        }

        return $category;
    }
}
