<?php

namespace Bonnier\Willow\Base\Actions\Backend;

use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use Bonnier\WP\Redirect\Http\BonnierRedirect;

/**
 * Class PostSlugChange
 *
 * @package \Bonnier\Willow\Base\Actions\Backend
 */
class PostSlugChange
{
    public function __construct()
    {
        add_filter('transition_post_status', [$this, 'transitionPostStatus'], 10, 3);
    }

    public function transitionPostStatus($newStatus, $oldStatus, \WP_Post $post)
    {
        if (in_array($oldStatus, ['draft', 'trash']) && $newStatus === 'publish') {
            $this->removeFromRedirectsForPost($post);
            $this->updateRedirectsForPost($post);
        }
        if ($oldStatus === 'publish' && in_array($newStatus, ['draft', 'trash'])) {
            $this->handlePostUnpublish($post, $newStatus);
        }
        if ($oldStatus === 'publish' && $newStatus === 'publish') {
            $this->handlePostSlugChange($post);
        }
    }

    public function createRedirect(\WP_Post $post, $oldLink, $newLink, $type = 'post-slug-change')
    {
        BonnierRedirect::createRedirect(
            parse_url($oldLink, PHP_URL_PATH),
            parse_url($newLink, PHP_URL_PATH),
            LanguageProvider::getPostLanguage($post->ID),
            $type,
            $post->ID
        );
    }

    private function removeFromRedirectsForPost(\WP_Post $post)
    {
        global $wpdb;
        $wpdb->suppress_errors(true);
        $wpdb->delete('wp_bonnier_redirects', [
            'wp_id' => $post->ID,
            'from' => parse_url($this->getNewPostLink($post), PHP_URL_PATH)
        ]);
    }

    private function handlePostUnpublish(\WP_Post $post, $newStatus)
    {
        $redirectTo = '/';
        if ($post->post_type !== 'page') {
            $categoryId = $post->post_category[0] ?? null;
            $category = get_category($categoryId);
            $redirectTo = ! is_wp_error($category) && $category->term_id && $categoryId != get_option('default_category') ?
                '/' . $category->slug : // Redirect to category page
                '/'; // Redirect to front page
        }
        $this->createRedirect($post, get_permalink(), $redirectTo, sprintf('post-status-change:%s', $newStatus));
    }

    private function handlePostSlugChange(\WP_Post $post)
    {
        $oldLink = get_permalink();
        $newLink = $this->getNewPostLink($post);
        if ($newLink && $oldLink !== $newLink) {
            $this->createRedirect($post, $oldLink, $newLink);
        }
    }

    private function updateRedirectsForPost($post)
    {
        $newPostPath = parse_url($this->getNewPostLink($post), PHP_URL_PATH);
        global $wpdb;
        $wpdb->suppress_errors(true);
        $wpdb->update('wp_bonnier_redirects',
            [
                'to' => $newPostPath,
                'to_hash' => md5($newPostPath),
                'type' => 'post-status-change:publish'
            ],
            [
                'wp_id' => $post->ID
            ]
        );
    }

    private function getNewPostLink(\WP_Post $post)
    {
        if (acf_validate_save_post()) {
            acf_save_post($post->ID); // Save post to make sure that potential category change is updated
            return get_permalink($post->ID);
        }
        return get_permalink($post->ID);
    }
}

