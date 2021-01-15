<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Terms\Tags;

use Bonnier\Willow\Base\Factories\CategoryContentFactory;
use Bonnier\Willow\Base\Helpers\AcfName;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\BannerPlacement;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\CommercialSpot;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\FeaturedContent;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\Newsletter;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\SeoText;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\TaxonomyList;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\TeaserList;
use Bonnier\Willow\Base\Models\Base\Root\Translation;
use Bonnier\Willow\Base\Models\WpComposite;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\Willow\Base\Adapters\Wp\AbstractWpAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Models\Contracts\Terms\TagContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;
use Bonnier\Willow\Base\Models\Base\Root\Teaser;
use Bonnier\Willow\Base\Traits\UrlTrait;
use Bonnier\WP\SiteManager\WpSiteManager;
use Illuminate\Support\Collection;
use WP_Query;
use WP_Term;

class TagAdapter extends AbstractWpAdapter implements TagContract
{
    use UrlTrait;

    protected $meta;
    protected $acfFields;
    protected $tagContents;
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
        AcfName::WIDGET_COMMERCIAL_SPOT => CommercialSpot::class,
    ];

    public function __construct(WP_Term $wpModel)
    {
        parent::__construct($wpModel);
        $this->meta = $this->getMeta();
        $this->acfFields = WpModelRepository::instance()->getAcfData(sprintf(
            '%s_%s',
            $this->wpModel->taxonomy ?? null,
            $this->wpModel->term_id ?? null
        ));
        $this->tagContents = array_get($this->acfFields, AcfName::GROUP_PAGE_WIDGETS) ?: null;
    }

    public function getId(): ?int
    {
        return data_get($this->wpModel, 'term_id') ?: null;
    }

    public function getName(): ?string
    {
        if ($name = data_get($this->wpModel, 'name')) {
            return htmlspecialchars_decode($name);
        }
        return null;
    }

    public function getTitle(): ?string
    {
        return data_get($this->meta, 'title.' . LanguageProvider::getCurrentLanguage()) ?: null;
    }

    public function getDescription(): ?string
    {
        return data_get($this->meta, 'description.' . LanguageProvider::getCurrentLanguage()) ?: null;
    }

    public function getSlug(): ?string
    {
        return data_get($this->wpModel, 'slug') ?: null;
    }

    public function getUrl(): ?string
    {
        if (($taxonomy = data_get($this->wpModel, 'taxonomy')) && $link = get_term_link($this->getId(), $taxonomy)) {
            return is_wp_error($link) ? null : $link;
        }

        return null;
    }

    public function getLanguage(): ?string
    {
        if ($tagId = $this->getId()) {
            return LanguageProvider::getTermLanguage($tagId);
        }

        return null;
    }

    public function getContentTeasers($page, $perPage, $orderBy, $order): Collection
    {
        $offset = $perPage * ($page - 1);
        return collect(get_posts([
            'post_type' => WpComposite::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => $perPage,
            'offset' => $offset,
            'orderby' => $orderBy,
            'order'  => $order,
            'tax_query' => [
                [
                    'taxonomy' => 'post_tag',
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
        $query = new WP_Query([
            'post_type' => WpComposite::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'tax_query' => [
                [
                    'taxonomy' => 'post_tag',
                    'field' => 'term_id',
                    'terms' => $this->getId(),
                    'include_children' => false,
                ]
            ]
        ]);

        return $query->found_posts ?: data_get($this->wpModel, 'count');
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->getFullUrl(get_tag_link($this->getId()));
    }

    public function getTeaser(string $type): ?TeaserContract
    {
        return new Teaser(new TagTeaserAdapter($this->meta, $type));
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

    public function getContents(int $page = 1): ?Collection
    {
        if (!$this->contents) {
            $this->contents = collect($this->tagContents)->map(function ($acfContentArray) use ($page) {
                $class = collect($this->contentModelsMapping)->get(array_get($acfContentArray, 'acf_fc_layout'));
                $model = $this->getContentFactory($class)->getModel($acfContentArray);
                if ($model instanceof TeaserList) {
                    $model->setParentId($this->getId());
                    $model->setParentType('post_tag');
                    $model->setCurrentPage($page);
                }
                return $model;
            })->reject(function ($content) {
                return is_null($content);
            });
        }

        return $this->contents;
    }

    public function getContenthubId(): ?string
    {
        return array_get($this->wpMeta, 'content_hub_id.0') ?: null;
    }

    private function getMeta()
    {
        if ($contentHubId = $this->getContenthubId()) {
            try {
                return WpSiteManager::instance()->tags()->findByContentHubId($contentHubId) ?? null;
            } catch (\Exception $exception) {
                return null;
            }
        }
        return null;
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
            if ($term instanceof WP_Term) {
                return [$locale => new Translation(new TagTranslationAdapter($term))];
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

    private function getContentFactory($class)
    {
        if ($this->contentFactory) {
            return $this->contentFactory->setBaseClass($class);
        }

        return $this->contentFactory = new CategoryContentFactory($class);
    }

    public function getInternal(): ?bool
    {
        $wpMeta = $this->getWpMeta();
        return data_get($wpMeta, 'internal.0') ?: false;
    }
}
