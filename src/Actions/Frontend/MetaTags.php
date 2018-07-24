<?php

namespace Bonnier\Willow\Base\Actions\Frontend;

class MetaTags
{
    public function __construct()
    {
        add_action('wp_head', [$this, 'addTitleToHead']);
        //SEO Remove link next && link prev.
        add_filter('wpseo_next_rel_link', '__return_false');
        add_filter('wpseo_prev_rel_link', '__return_false');
    }

    public function addTitleToHead()
    {
        echo '<title>'.wp_title('|', false).'</title>';
    }
}
