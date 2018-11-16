<?php


namespace Bonnier\Willow\Base\Adapters\Wp\Pages;

use Bonnier\Willow\Base\Adapters\Wp\AbstractWpAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\AuthorAdapter;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Factories\PageContentFactory;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\BannerPlacement;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\FeaturedContent;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\Newsletter;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\TaxonomyList;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\TeaserList;
use Bonnier\Willow\Base\Models\Base\Pages\PageTranslation;
use Bonnier\Willow\Base\Models\Base\Root\Author;
use Bonnier\Willow\Base\Models\Base\Root\Teaser;
use Bonnier\Willow\Base\Models\Contracts\Pages\PageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;
use Bonnier\Willow\Base\Traits\DateTimeZoneTrait;
use Bonnier\Willow\Base\Traits\UrlTrait;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\ContentHub\Editor\Helpers\AcfName;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\SeoText;
use DateTime;
use Illuminate\Support\Collection;
use WP_Post;

/**
 * Class PageAdapter
 * @package Bonnier\Willow\Base\Adapters\Wp\Pages
 *
 * @property \WP_Post $wpModel
 */
class PageAdapter extends AbstractWpAdapter implements PageContract
{
    use DateTimeZoneTrait, UrlTrait;

    protected $acfFields;
    protected $pageContents;
    protected $contents;
    /** @var PageContentFactory */
    protected $contentFactory;

    protected $contentModelsMapping = [
        AcfName::WIDGET_TEASER_LIST => TeaserList::class,
        AcfName::WIDGET_FEATURED_CONTENT => FeaturedContent::class,
        AcfName::WIDGET_SEO_TEXT => SeoText::class,
        AcfName::WIDGET_NEWSLETTER => Newsletter::class,
        AcfName::WIDGET_BANNER_PLACEMENT => BannerPlacement::class,
        AcfName::WIDGET_TAXONOMY_TEASER_LIST => TaxonomyList::class,
    ];

    public function __construct(WP_Post $page)
    {
        parent::__construct($page);

        $this->acfFields = WpModelRepository::instance()->getAcfData($this->wpModel->ID);
        $this->pageContents = array_get($this->acfFields, AcfName::GROUP_PAGE_WIDGETS) ?: null;
    }

    public function getAcfFields()
    {
        return $this->acfFields;
    }

    public function getId(): int
    {
        return data_get($this->wpModel, 'ID', 0);
    }

    public function getTitle(): ?string
    {
        return data_get($this->wpModel, 'post_title') ?: null;
    }

    public function getContent(): ?string
    {
        return data_get($this->wpModel, 'post_content') ?: null;
    }

    public function getStatus(): ?string
    {
        return data_get($this->wpModel, 'post_status') ?: null;
    }

    public function getAuthor(): ?AuthorContract
    {
        if (($author = data_get($this->wpModel, 'post_author')) && $wpUser = get_user_by('id', $author)) {
            return new Author(new AuthorAdapter($wpUser));
        }

        return null;
    }

    public function getTemplate(): ?string
    {
        return get_page_template_slug($this->getId()) ?: null;
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

    public function isFrontPage(): bool
    {
        return intval(get_option('page_on_front')) === $this->getId();
    }

    public function getTeaser(string $type): ?TeaserContract
    {
        return new Teaser(new PageTeaserAdapter($this, $type));
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
        return $this->getFullUrl(get_permalink($this->getId()));
    }

    public function getContents(): ?Collection
    {
        if (!$this->contents) {
            $this->contents = collect($this->pageContents)->map(function ($acfContentArray) {
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
        $pageTranslations = LanguageProvider::getPostTranslations($this->getId());
        $translations = collect($pageTranslations)->mapWithKeys(function (int $pageId, string $locale) {
            if ($pageId === $this->getId()) {
                $page = $this->wpModel;
            } else {
                $page = WpModelRepository::instance()->getPost($pageId);
            }
            if ($page instanceof WP_Post) {
                return [$locale => new PageTranslation(new PageTranslationAdapter($page))];
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

    private function getContentFactory($class)
    {
        if ($this->contentFactory) {
            return $this->contentFactory->setBaseClass($class);
        }

        return $this->contentFactory = new PageContentFactory($class);
    }
}
