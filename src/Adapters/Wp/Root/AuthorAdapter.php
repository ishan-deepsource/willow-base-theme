<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Adapters\Wp\Composites\CompositeAdapter;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Models\WpComposite;
use Bonnier\Willow\Base\Models\WpUserProfile;
use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use DateTime;
use Illuminate\Support\Collection;
use WP_User;

/**
 * Class AuthorAdapter
 *
 * @package \Bonnier\Willow\Base\Adapters\Wp
 */
class AuthorAdapter implements AuthorContract
{
    protected $user;
    protected $meta;

    /**
     * AuthorAdapter constructor.
     *
     * @param $user
     */
    public function __construct(WP_User $user = null)
    {
        $this->user = $user;
        $this->meta = WpModelRepository::instance()->getUserMeta($this->user);
    }

    public function getId(): ?int
    {
        return data_get($this->user, 'ID') ?: null;
    }

    public function getName(): ?string
    {
        // Decode to ensure '&amp;' is converted to '&'
        return htmlspecialchars_decode(data_get($this->user, 'display_name')) ?: null;
    }

    public function getBiography(): ?string
    {
        return data_get($this->user, 'description') ?: null;
    }

    public function getAvatar(): ?ImageContract
    {
        if ($imageId = intval(array_get($this->meta, 'user_avatar.0'))) {
            if ($image = WpModelRepository::instance()->getPost($imageId)) {
                return new Image(new ImageAdapter($image));
            }
        }
        return null;
    }

    public function getUrl(): ?string
    {
        $displayNameAsSlug = sanitize_title(data_get($this->user, 'display_name') ?: null);
        $path = '/author/' . $displayNameAsSlug;
        if ($this->isAuthor()) {
            $path = '/author';
        }
        if ($path) {
            return LanguageProvider::getHomeUrl($path);
        }
        return null;
    }

    public function getEmail(): ?string
    {
        return data_get($this->user, 'user_email') ?: null;
    }

    public function getWebsite(): ?string
    {
        return data_get($this->user, 'user_url') ?: null;
    }

    public function getTitle(): ?string
    {
        return WpUserProfile::getTitle($this->getId()) ?: null;
    }

    public function getEducation(): ?string
    {
        return WpUserProfile::getEducation($this->getId()) ?: null;
    }

    public function getContentTeasers($page, $perPage, $orderBy, $order, $offset): Collection
    {
        global $wpdb;
        $excludedFromWebIds = $wpdb->get_col("SELECT post_id FROM wp_postmeta WHERE meta_key='exclude_platforms' and meta_value like '%web%'");        
        $offset = $offset ?: ($perPage * ($page - 1));
        return collect(get_posts([
            'post_type' => WpComposite::POST_TYPE,
            'post_status' => 'publish',
            'post__not_in' => $excludedFromWebIds,
            'author' => $this->getId(),
            'posts_per_page' => $perPage,
            'offset' => $offset,
            'orderby' => $orderBy,
            'order'  => $order,
        ]))->map(function (\WP_Post $post) {
            $composite = WpModelRepository::instance()->getPost($post);
            return new Composite(new CompositeAdapter($composite));
        });
    }

    public function getBirthday(): ?DateTime
    {
        if ($birthday = array_get($this->meta, 'birthday.0')) {
            return new DateTime($birthday);
        }
        return null;
    }

    public function isPublic(): bool
    {
        return array_get($this->meta, 'public.0') === '1';
    }

    public function isAuthor(): bool
    {
        return array_get($this->meta, 'author.0') === '1';
    }

    public function getCount(): int
    {
        $args = array(
            'post_type' => WpComposite::POST_TYPE,
            'author' => $this->getId(),
            'lang' => LanguageProvider::getCurrentLanguage(),
        );
        $authorQuery = new \WP_Query($args);
        return $authorQuery->found_posts ?: 0;
    }
}
