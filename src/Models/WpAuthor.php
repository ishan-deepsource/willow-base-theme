<?php

namespace Bonnier\Willow\Base\Models;

class WpAuthor
{
    private static $defaultAuthors = [
        'da' => 'Redaktionen',
        'sv' => 'Redaktionen',
        'nb' => 'Redaksjonen',
        'fi' => 'Toimitus',
        'nl' => 'De redactie',
    ];

    public static function getDefaultAuthors()
    {
        return self::$defaultAuthors;
    }

    public static function getDefaultAuthor($locale): ?\WP_User
    {
        $displayName = array_get(self::$defaultAuthors, $locale ?? 'da', 'Redaktionen');
        $authorName = $displayName . '_' . $locale;
        return self::findOrCreate($authorName, $displayName);
    }

    public static function findOrCreate($authorName, $displayName = ''): ?\WP_User
    {
        $contentHubId = self::makeContentHubId($authorName);
        if ($existingId = self::getByContentHubId($contentHubId)) {
            return get_userdata($existingId);
        }
        return self::createAuthor($authorName, $displayName, $contentHubId);
    }

    private static function createAuthor($login, $displayName, $contentHubId): ?\WP_User
    {
        $userId = wp_insert_user([
            'ID'           => null,
            'user_login'   => sanitize_user($login),
            'display_name' => $displayName,
            'user_pass'    => md5(rand(1, 999999)),
            'role'         => 'author',
        ]);
        if (is_wp_error($userId)) {
            return null;
        }

        update_user_meta($userId, 'contenthub_id', $contentHubId);
        return get_userdata($userId);
    }

    private static function makeContentHubId($authorName)
    {
        return base64_encode(sprintf('wa-author-%s', md5(strtolower(trim($authorName)))));
    }

    private static function getByContentHubId($id)
    {
        global $wpdb;
        return  $wpdb->get_var(
            $wpdb->prepare(
                "SELECT user_id FROM wp_usermeta WHERE meta_key=%s AND meta_value=%s",
                'contenthub_id',
                $id
            )
        );
    }
}
