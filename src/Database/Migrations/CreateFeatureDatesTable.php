<?php

namespace Bonnier\Willow\Base\Database\Migrations;

use Bonnier\Willow\Base\Database\DB;
use Illuminate\Database\Schema\Blueprint;

class CreateFeatureDatesTable implements Migration
{
    public static function migrate()
    {
        if (self::verify()) {
            return;
        }

        global $wpdb;
        $table = sprintf('%sfeature_dates', $wpdb->prefix);
        $charset = $wpdb->get_charset_collate();

        $sql = "
        SET sql_notes = 1;
        CREATE TABLE `$table` (
          `post_id` int(10) unsigned NOT NULL,
          `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          UNIQUE KEY `feature_dates_post_id_unique` (`post_id`)
        ) $charset;
        SET sql_notes = 1;
        ";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function verify(): bool
    {
        global $wpdb;
        $table = sprintf('%sfeature_dates', $wpdb->prefix);
        return $wpdb->get_var("SHOW TABLES LIKE '$table'") === $table;
    }
}
