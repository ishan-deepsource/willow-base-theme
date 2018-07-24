<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\WP\ContentHub\Editor\Models\WpComposite;
use Bonnier\Willow\Base\Models\Contracts\Root\SitemapItemContract;
use Bonnier\Willow\Base\Traits\UrlTrait;
use DateTime;
use WP_Query;
use WP_Term;

class SitemapTagAdapter implements SitemapItemContract
{
    use UrlTrait;

    protected $tag;

    /**
     * SitemapTermAdapter constructor.
     * @param WP_Term $tag
     */
    public function __construct(WP_Term $tag)
    {
        $this->tag = $tag;
    }


    public function getUrl(): string
    {
        return $this->getFullUrl(get_tag_link($this->tag->term_id));
    }

    public function getLastModified(): DateTime
    {
        $query = new WP_Query([
            'post_type' => WpComposite::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'orderby' => 'modified',
            'order' => 'DESC',
            'tax_query' => [
                [
                    'taxonomy' => 'post_tag',
                    'field' => 'term_id',
                    'terms' => $this->tag->term_id,
                    'include_children' => false,
                ]
            ]
        ]);

        if ($query->have_posts()) {
            return new DateTime($query->posts[0]->post_modified_gmt);
        }

        return new DateTime();
    }
}
