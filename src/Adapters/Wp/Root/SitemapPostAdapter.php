<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\SitemapItemContract;
use Bonnier\Willow\Base\Traits\UrlTrait;
use DateTime;
use WP_Post;

class SitemapPostAdapter implements SitemapItemContract
{
    use UrlTrait;

    protected $post;

    /**
     * SitemapPostAdapter constructor.
     * @param $post
     */
    public function __construct(WP_Post $post)
    {
        $this->post = $post;
    }

    public function getUrl(): string
    {
        return $this->getFullUrl(get_permalink($this->post->ID));
    }

    public function getLastModified(): DateTime
    {
        return new DateTime($this->post->post_modified_gmt);
    }
}
