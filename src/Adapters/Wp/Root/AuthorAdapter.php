<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Repositories\WpModelRepository;
use Bonnier\Willow\Base\Models\Base\Root\Image;
use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use Bonnier\Willow\Base\Models\Contracts\Root\ImageContract;
use Bonnier\WP\ContentHub\Editor\Models\WpUserProfile;
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
        return data_get($this->user, 'display_name') ?: null;
    }

    public function getBiography(): ?string
    {
        return data_get($this->user, 'description') ?: null;
    }

    public function getAvatar(): ?ImageContract
    {
        if ($imageId = array_get($this->meta, 'user_avatar.0')) {
            $image = WpModelRepository::instance()->getPost($imageId);
            return new Image(new ImageAdapter($image));
        }
        return null;
    }

    public function getUrl(): ?string
    {
        return get_author_posts_url($this->getId()) ?: null;
    }

    public function getTitle(): ?string
    {
        return WpUserProfile::getTitle($this->getId()) ?: null;
    }
}
