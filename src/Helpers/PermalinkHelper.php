<?php

namespace Bonnier\Willow\Base\Helpers;

class PermalinkHelper
{
    public static $customPermalink = 'willow_custom_permalink';

    /**
     * protect the endpoints from being vaped if a category/tag slug is set
     * @var $endpoints
     */
    protected $endpoints;

    /**
     * Reusable object instance.
     *
     * @var PermalinkHelper
     */
    protected static $instance = null;

    /**
     * Creates a new instance. Called on 'init'.
     * May be used to access class methods from outside.
     *
     * @see    __construct()
     * @return PermalinkHelper
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public function __construct()
    {
        // Late init to protect endpoints
        add_action('init', array( $this, 'late_init' ), 999);

        // add our new peramstructs to the rewrite rules
        add_filter('post_rewrite_rules', array( $this, 'custom_rules' ));

        // parse the generated links - enable custom taxonomies for built in post links
        add_filter('post_link', array( $this, 'parse_permalinks' ), 10, 3);
        add_filter('post_type_link', array( $this, 'parse_permalinks' ), 10, 4);
    }


    public function late_init()
    {
        global $wp_rewrite;
        $this->endpoints = $wp_rewrite->endpoints;
    }

    /**
     * This function removes unnecessary rules and adds in the new rules
     *
     * @param array $rules The rewrite rules array for post permalinks
     *
     * @return array    The modified rules array
     */
    public function custom_rules($rules)
    {
        global $wp_rewrite;

        // restore endpoints
        if (empty($wp_rewrite->endpoints) && ! empty($this->endpoints)) {
            $wp_rewrite->endpoints = $this->endpoints;
        }

        $permalink_structures = array( $wp_rewrite->permalink_structure => array( 'post' ) );

        /*
         * The ‘use_verbose_page_rules’ object property will be set to true if the permalink structure begins with
         * one of the following: ‘%postname%’, ‘%category%’, ‘%tag%’, or ‘%author%’.
         *
         * Setting it to false will force page rewrite to bottom
         */
        $wp_rewrite->use_verbose_page_rules = false;

        // get $permalink_structures foreach custom post type and group any that use the same struct
        foreach (get_post_types(array( '_builtin' => false, 'public' => true ), 'objects') as $type) {
            // check we have a custom permalink structure
            if (! is_array($type->rewrite) || ! isset($type->rewrite[ static::$customPermalink ])) {
                continue;
            }

            // remove default struct rules
            add_filter($type->name . '_rewrite_rules', function ($rules) {
                return array();
            }, 11);

            if (! isset($permalink_structures[ $type->rewrite[ static::$customPermalink ] ])) {
                $permalink_structures[ $type->rewrite[ static::$customPermalink ] ] = array();
            }

            $permalink_structures[ $type->rewrite[ static::$customPermalink ] ][] = $type->name;
        }

        $rules = array();

        // add our $permalink_structures scoped to the post types - overwriting any keys that already exist
        foreach ($permalink_structures as $struct => $post_types) {
            // if a struct is %postname% only then we need page rules first
            // - if not found wp tries again with later rules
            if (preg_match('/^\/?%postname%\/?$/', $struct)) {
                $wp_rewrite->use_verbose_page_rules = true;
            }

            // get rewrite rules without walking dirs
            $post_type_rules_temp = $wp_rewrite->generate_rewrite_rules(
                $struct,
                EP_PERMALINK,
                false,
                true,
                false,
                false,
                true
            );

            foreach ($post_type_rules_temp as $regex => $query) {
                if (preg_match('/([&?])(cpage|attachment|p|name|pagename)=/', $query)) {
                    if (count($post_types) < 2) {
                        $post_type_query = sprintf('&post_type=%s', $post_types[0]);
                    } else {
                        $post_type_query = sprintf('&post_type[]=%s', join('&post_type[]=', array_unique($post_types)));
                    }
                    $rules[ $regex ] = sprintf(
                        '%s%s',
                        $query,
                        preg_match('/([&?])(attachment|pagename)=/', $query) ? '' : $post_type_query
                    );
                } else {
                    unset($rules[ $regex ]);
                }
            }
        }

        return $rules;
    }


    /**
     * Generic version of standard permalink parsing function. Adds support for
     * custom taxonomies as well as the standard %author% etc...
     *
     * @param string $post_link The post URL
     * @param \WP_Post $post The post object
     * @param bool $leavename Passed to pre_post_link filter
     * @param bool $sample Used in admin if generating an example permalink
     *
     * @return string    The parsed permalink
     */
    public function parse_permalinks($post_link, \WP_Post $post, $leavename, $sample = false)
    {
        //Guard clause
        if (!post_type_exists($post->post_type)) {
            return $post_link;
        }

        $rewritecode = array(
            '%year%',
            '%monthnum%',
            '%day%',
            '%hour%',
            '%minute%',
            '%second%',
            $leavename ? '' : '%postname%',
            '%post_id%',
            '%author%',
            $leavename ? '' : '%pagename%',
        );

        $taxonomies = get_object_taxonomies($post->post_type);

        foreach ($taxonomies as $taxonomy) {
            $rewritecode[] = '%' . $taxonomy . '%';
        }

        if (is_object($post) && isset($post->filter) && 'sample' == $post->filter) {
            $sample = true;
        }

        $post_type = get_post_type_object($post->post_type);

        // prefer option over default
        if (
            isset($post_type->rewrite[static::$customPermalink]) &&
            ! empty($post_type->rewrite[static::$customPermalink])
        ) {
            $permalink = $post_type->rewrite[ static::$customPermalink ];
        } else {
            return $post_link;
        }

        $permalink = apply_filters('pre_post_link', $permalink, $post, $leavename);

        if ('' != $permalink && !in_array($post->post_status, array('draft', 'pending', 'auto-draft'))) {
            $unixtime = strtotime($post->post_date);

            // add ability to use any taxonomies in post type static::$custom_permalink
            $replace_terms = array();
            foreach ($taxonomies as $taxonomy) {
                $term = '';
                $taxonomy_object = get_taxonomy($taxonomy);

                if (strpos($permalink, '%'. $taxonomy . '%') !== false) {
                    $terms = get_the_terms($post->ID, $taxonomy);
                    if ($terms) {
                        usort($terms, '_usort_terms_by_ID'); // order by ID
                        $term = $terms[0]->slug;
                        if ($taxonomy_object->hierarchical && $parent = $terms[0]->parent) {
                            $term = $this->get_term_parents($parent, $taxonomy, false, '/', true) . $term;
                        }
                    }
                    // show default category in permalinks, without
                    // having to assign it explicitly
                    if (empty($term) && $taxonomy == 'category') {
                        $default_category = get_category(get_option('default_category'));
                        $term = is_wp_error($default_category) ? '' : $default_category->slug;
                    }
                }

                $replace_terms[ $taxonomy ] = $term;
            }

            $author = '';
            if (strpos($permalink, '%author%') !== false) {
                $authordata = get_userdata($post->post_author);
                $author = $authordata->user_nicename;
            }

            $date = explode(" ", date('Y m d H i s', $unixtime));
            $rewritereplace =
                array(
                    $date[0],
                    $date[1],
                    $date[2],
                    $date[3],
                    $date[4],
                    $date[5],
                    $post->post_name,
                    $post->ID,
                    $author,
                    $post->post_name,
                );

            foreach ($taxonomies as $taxonomy) {
                $rewritereplace[] = $replace_terms[ $taxonomy ];
            }

            //https://developer.wordpress.org/reference/functions/get_permalink/
            $permalink = home_url(str_replace($rewritecode, $rewritereplace, $permalink));
            $permalink = user_trailingslashit($permalink, 'single');
        } else { // if they're not using the fancy permalink option
            $permalink = home_url("?post_type=$post->post_type&p=$post->ID");
        }

        return $permalink;
    }

    private function get_term_parents($id, $taxonomy, $link = false, $separator = '/', $nicename = false, $visited = [])
    {
        $chain = '';
        $parent = get_term($id, $taxonomy);
        if (is_wp_error($parent)) {
            return $parent;
        }

        if ($nicename) {
            $name = $parent->slug;
        } else {
            $name = $parent->cat_name;
        }

        if ($parent->parent && ($parent->parent != $parent->term_id) && !in_array($parent->parent, $visited)) {
            $visited[] = $parent->parent;
            $chain .= $this->get_term_parents($parent->parent, $taxonomy, $link, $separator, $nicename, $visited);
        }

        if ($link) {
            $chain .= sprintf(
                '<a href="%s" title="%s">%s</a> %s',
                get_term_link($parent->term_id, $taxonomy),
                esc_attr(sprintf(__("View all posts in %s"), $parent->name)),
                $name,
                $separator
            );
        } else {
            $chain .= sprintf('%s%s', $name, $separator);
        }

        return $chain;
    }
}
