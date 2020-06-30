<?php

namespace Bonnier\Willow\Base\Helpers;

use Bonnier\Willow\Base\Models\WpComposite;
use WP_Post;

class FocalPoint
{
    public const FIELD = 'focal_point';
    private static $instance;

    private function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'registerScripts']);
        add_filter('attachment_fields_to_edit', [$this, 'addFields'], 11, 2);
        add_filter('attachment_fields_to_save', [$this, 'saveFields'], 11, 2);
        add_filter('pll_copy_post_metas', [$this, 'copy_post_metas'], 10, 5);
    }

    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function addFields($formFields, WP_Post $post = null)
    {
        if (!$post) {
            return $formFields;
        }
        if (preg_match("/image/", $post->post_mime_type)) {
            $meta = get_post_meta($post->ID, '_' . static::FIELD, true);

            $formFields[static::FIELD] = [
                'label' => 'Focal point',
                'input' => 'text',
                'value' => $meta
            ];
        }

        return $formFields;
    }

    public function saveFields($post, $attachment)
    {
        if (isset($attachment[static::FIELD])) {
            if (!preg_match('/[0,1]\.[0-9]{1,2}\,[0,1]\.[0-9]{1,2}/', $attachment[static::FIELD])) {
                $post['errors'][static::FIELD]['errors'][] = 'Invalid focal point format';
            } else {
                update_post_meta($post['ID'], '_' . static::FIELD, $attachment[static::FIELD]);
            }
        }

        return $post;
    }

    public function registerScripts($hook)
    {
        if (! in_array($hook, ['post.php', 'post-new.php'])) {
            return;
        }

        wp_register_script(
            'focal_point_class',
            get_theme_file_uri('/assets/js/FocalPoint.js'),
            [],
            filemtime(get_theme_file_path('/assets/js/FocalPoint.js'))
        );
        wp_localize_script('focal_point_class', 'assets', [
            'crosshair' => get_theme_file_uri('/assets/img/crosshair.png'),
        ]);

        if (wp_attachment_is_image()) {
            wp_register_script(
                'focal_point_script',
                get_theme_file_uri('/assets/js/edit_image_focalpoint.js'),
                ['focal_point_class'],
                filemtime(get_theme_file_path('/assets/js/edit_image_focalpoint.js'))
            );
            wp_enqueue_script('focal_point_script');
        } elseif (get_post_type() === WpComposite::POST_TYPE) {
            wp_register_script(
                'focal_point_composite_images',
                get_theme_file_uri('/assets/js/composite_image_focalpoint.js'),
                ['acf-input', 'focal_point_class'],
                filemtime(get_theme_file_path('/assets/js/composite_image_focalpoint.js'))
            );
            wp_enqueue_script('focal_point_composite_images');
        }

        wp_enqueue_script('focal_point_class');
    }

    public function copy_post_metas($metas)
    {
        return array_merge($metas, array( '_focal_point'));
    }
}
