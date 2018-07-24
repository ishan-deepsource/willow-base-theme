<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Root;

use Bonnier\WP\ContentHub\Editor\Models\WpComposite;
use Bonnier\Willow\Base\Models\Contracts\Root\SitemapItemContract;
use Bonnier\Willow\Base\Traits\UrlTrait;
use DateTime;
use WP_Query;
use WP_Term;

class SitemapCategoryAdapter implements SitemapItemContract
{
    use UrlTrait;

    protected $category;

    /**
     * SitemapTermAdapter constructor.
     * @param WP_Term $category
     */
    public function __construct(WP_Term $category)
    {
        $this->category = $category;
    }


    public function getUrl(): string
    {
        return $this->getFullUrl(get_category_link($this->category->term_id));
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
                    'taxonomy' => 'category',
                    'field' => 'term_id',
                    'terms' => $this->category->term_id,
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
