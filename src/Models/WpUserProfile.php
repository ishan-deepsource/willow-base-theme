<?php

namespace Bonnier\Willow\Base\Models;

use Bonnier\Willow\Base\Models\ACF\User\UserFieldGroup;

class WpUserProfile
{
    public static function register()
    {
        UserFieldGroup::register();
        if (is_admin()) {
            self::modifyAvatarFilter();
        }
        
        add_action('admin_enqueue_scripts', [__CLASS__, 'loadAuthorDescriptionAsMarkdownScript']);
    }

    public static function getAvatarFromUser($userId): ?\WP_Post
    {
        $avatarImageId = get_user_meta($userId, 'user_avatar', true) ?: null;
        return $avatarImageId ? get_post($avatarImageId) : null;
    }

    public static function getTitle($userId): ?string
    {
        return get_field('user_title', sprintf('user_%s', $userId));
    }

    private static function modifyAvatarFilter()
    {
        add_filter('get_avatar_url', function ($url, $idOrEmail, $args) {
            $user = false;

            if (is_numeric($idOrEmail)) {
                $userId = (int)$idOrEmail;
                $user = get_user_by('id', $userId);
            } elseif (is_object($idOrEmail)) {
                if (! empty($idOrEmail->user_id)) {
                    $userId = (int)$idOrEmail->user_id;
                    $user = get_user_by('id', $userId);
                }
            } else {
                $user = get_user_by('email', $idOrEmail);
            }

            if ($user && is_object($user)) {
                if (!isset($user->data->ID)) {
                    return $url;
                }
                if ($avatarId = get_user_meta($user->data->ID, 'user_avatar', true)) {
                    $imageSizeParams = sprintf('?auto=compress&w=%s&h=%s', $args['height'], $args['height']);
                    $url = wp_get_attachment_thumb_url($avatarId) . $imageSizeParams;
                }
            }

            return $url;
        }, 10, 3);
    }

    public static function loadAuthorDescriptionAsMarkdownScript($admin_page)
    {
        if ($admin_page === 'user-edit.php') {
            wp_register_script(
                'author-description-as-markdown',
                get_theme_file_uri('/assets/js/author-description-as-markdown.js'),
                ['acf-input'],
                filemtime(get_theme_file_path('/assets/js/author-description-as-markdown.js'))
            );
            wp_enqueue_script(
                'author-description-as-markdown',
                '',
                [],
                filemtime(get_theme_file_path('/assets/js/author-description-as-markdown.js'))
            );
        };
    }
}
