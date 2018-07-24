<?php


namespace Bonnier\Willow\Base\Adapters\Wp\Pages;

use Bonnier\Willow\Base\Adapters\Wp\AbstractWpAdapter;
use Bonnier\Willow\Base\Adapters\Wp\Root\AuthorAdapter;
use Bonnier\Willow\Base\Models\Base\Root\Author;
use Bonnier\Willow\Base\Models\Base\Root\Teaser;
use Bonnier\Willow\Base\Models\Contracts\Pages\PageContract;
use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use Bonnier\Willow\Base\Models\Contracts\Root\TeaserContract;
use Bonnier\Willow\Base\Traits\DateTimeZoneTrait;
use Bonnier\Willow\Base\Traits\UrlTrait;
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

    public function __construct(WP_Post $page)
    {
        parent::__construct($page);

        $this->acfFields = get_fields($this->wpModel->ID);
    }

    public function getAcfFields()
    {
        return $this->acfFields;
    }

    public function getId(): int
    {
        return $this->wpModel->ID;
    }

    public function getTitle(): ?string
    {
        return $this->wpModel->post_title;
    }

    public function getContent(): ?string
    {
        return $this->wpModel->post_content;
    }

    public function getStatus(): ?string
    {
        return $this->wpModel->post_status;
    }

    public function getAuthor(): ?AuthorContract
    {
        if ($wpUser = get_user_by('id', $this->wpModel->post_author)) {
            return new Author(new AuthorAdapter($wpUser));
        }

        return null;
    }

    public function getTemplate(): ?string
    {
        return get_page_template_slug($this->getId());
    }

    public function getPublishedAt(): ?DateTime
    {
        return $this->toDateTime($this->wpModel->post_date);
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->toDateTime($this->wpModel->post_modified);
    }

    public function isFrontPage(): bool
    {
        return intval(get_option('page_on_front')) === $this->wpModel->ID;
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
}
