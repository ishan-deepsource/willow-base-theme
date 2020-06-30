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

    private static function addCustomFields()
    {
        if (function_exists('acf_add_local_field_group')) :
            acf_add_local_field_group([
                'key' => 'group_5ad5e82740549',
                'title' => 'Willow User Profile',
                'fields' => [
                    [
                        'key' => 'field_5ad5e867977d3',
                        'label' => 'Profile Picture',
                        'name' => 'user_avatar',
                        'type' => 'image',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => [
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ],
                        'return_format' => 'array',
                        'preview_size' => 'medium',
                        'library' => 'all',
                        'min_width' => '',
                        'min_height' => '',
                        'min_size' => '',
                        'max_width' => '',
                        'max_height' => '',
                        'max_size' => '',
                        'mime_types' => '',
                    ],
                    [
                        'key' => 'field_5af17b5df8440',
                        'label' => 'Title',
                        'name' => 'user_title',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => [
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ],
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ],
                    [
                        'key' => 'field_5e6e0ca2219b4',
                        'label' => 'Birthday',
                        'name' => 'birthday',
                        'type' => 'date_picker',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => [
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ],
                        'display_format' => 'd/m/Y',
                        'return_format' => 'd/m/Y',
                        'first_day' => 1,
                    ],
                    [
                        'key' => 'field_5e6e0cdd219b5',
                        'label' => 'Public',
                        'name' => 'public',
                        'type' => 'true_false',
                        'instructions' => 'Should this author have an author page and be on sitemaps in the frontend?',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => [
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ],
                        'message' => '',
                        'default_value' => 0,
                        'ui' => 0,
                        'ui_on_text' => '',
                        'ui_off_text' => '',
                    ],
                ],
                'location' => [
                    [
                        [
                            'param' => 'user_form',
                            'operator' => '==',
                            'value' => 'edit',
                        ],
                    ],
                ],
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => 1,
                'description' => '',
            ]);

        endif;
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
