<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Terms\Tags;

use Bonnier\Willow\Base\Models\Base\Terms\TagTranslation;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\ContentHub\Editor\Models\WpComposite;
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

    public function __construct(WP_Term $wpModel, ?array $meta)
    {
        parent::__construct($wpModel, $meta);
        $this->meta = $this->getMeta();
    }

    public function getId(): ?int
    {
        return data_get($this->wpModel, 'term_id') ?: null;
    }

    public function getName(): ?string
    {
        return data_get($this->wpModel, 'name') ?: null;
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
            $meta = get_post_meta($post->ID);
            return new Composite(new CompositeAdapter($post, $meta));
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

    private function getMeta()
    {
        if ($contentHubId = get_term_meta($this->getId(), 'content_hub_id', true)) {
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
                return [$locale => new TagTranslation(new TagTranslationAdapter($term))];
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
}
