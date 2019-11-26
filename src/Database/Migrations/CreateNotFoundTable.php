<?php

namespace Bonnier\Willow\Base\Database\Migrations;

class CreateNotFoundTable implements Migration
{

    /**
     * Run the migration
     */
    public static function migrate()
    {
        if (self::verify()) {
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . Migrate::NOT_FOUND_TABLE;
        $charset = $wpdb->get_charset_collate();

        $sql = "
        SET sql_notes = 1;
        CREATE TABLE `$table` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `url` text CHARACTER SET utf8 NOT NULL,
          `url_hash` char(32) COLLATE utf8mb4_unicode_520_ci NOT NULL,
          `locale` varchar(2) CHARACTER SET utf8 NOT NULL,
          `hits` int(11) unsigned NOT NULL DEFAULT 0,
          `notification_sent` tinyint(1) unsigned NOT NULL DEFAULT 0,
          `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `from_hash` (`url_hash`,`locale`)
        ) $charset;
        SET sql_notes = 1;
        ";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Verify that the migration was run successfully
     *
     * @return bool
     */
    public static function verify(): bool
    {
        global $wpdb;
        $table = $wpdb->prefix . Migrate::NOT_FOUND_TABLE;
        return $wpdb->get_var("SHOW TABLES LIKE '$table'") === $table;
    }
}
