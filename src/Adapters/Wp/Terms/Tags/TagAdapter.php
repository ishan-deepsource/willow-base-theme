<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Terms\Tags;

use Bonnier\Willow\MuPlugins\LanguageProvider;
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

    public function __construct(WP_Term $wpModel)
    {
        parent::__construct($wpModel);
        $this->meta = $this->getMeta();
    }

    public function getId(): ?int
    {
        return $this->wpModel->term_id;
    }

    public function getName(): ?string
    {
        return $this->wpModel->name;
    }

    public function getTitle(): ?string
    {
        return $this->meta->title->{LanguageProvider::getCurrentLanguage()} ?? null;
    }

    public function getDescription(): ?string
    {
        return $this->meta->description->{LanguageProvider::getCurrentLanguage()} ?? null;
    }

    public function getSlug(): ?string
    {
        return $this->wpModel->slug;
    }

    public function getUrl(): ?string
    {
        $link = get_term_link($this->getId(), 'post_tag');
        return is_wp_error($link) ? null : $link;
    }

    public function getLanguage(): ?string
    {
        return LanguageProvider::getTermLanguage($this->getId());
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
            return new Composite(new CompositeAdapter($post));
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

        return $query->found_posts ?: $this->wpModel->count;
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
        $contentHubId = get_term_meta($this->getId(), 'content_hub_id', true);
        if ($contentHubId) {
            try {
                $tag = WpSiteManager::instance()->tags()->findByContentHubId($contentHubId) ?? null;

                return $tag;
            } catch (\Exception $exception) {
                return null;
            }
        }
        return null;
    }
}
