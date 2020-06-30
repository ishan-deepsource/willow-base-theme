<?php

namespace Bonnier\Willow\Base\Commands;

use WP_CLI;
use WP_CLI_Command;

/**
 * Class AdvancedCustomFields
 */
class AdvancedCustomFields extends WP_CLI_Command
{
    private const EXPORT_DIR = WP_CONTENT_DIR . '/acf-exports/';
    private const CMD_NAMESPACE = 'acf';

    public static function register()
    {
        WP_CLI::add_command(CmdManager::CORE_CMD_NAMESPACE  . ' ' . static::CMD_NAMESPACE, __CLASS__);
    }

    /**
     * Dumps ACF fields defined in code to a JSON importable file
     *
     * ## EXAMPLES
     *
     * wp contenthub editor acf dump
     *
     */
    public function dump()
    {
        $groups = acf_get_local_field_groups();
        $json = [];

        foreach ($groups as $group) {
            // Fetch the fields for the given group key
            $fields = acf_get_local_fields($group['key']);

            // Remove unecessary key value pair with key "ID"
            unset($group['ID']);

            // Add the fields as an array to the group
            $group['fields'] = $fields;

            // Add this group to the main array
            $json[] = $group;
        }

        $json = json_encode($json, JSON_PRETTY_PRINT);

        // Make sure export dir exists
        if (!file_exists(static::EXPORT_DIR)) {
            mkdir(static::EXPORT_DIR, 0777, true);
        }

        // Write output to file for easy import into ACF.
        $file = static::EXPORT_DIR . 'export.json';
        if (file_put_contents($file, $json)) {
            WP_CLI::success("Successfully Dumped JSON to: " . $file);
        } else {
            WP_CLI::error("Failed dumping file, please check that " . WP_CONTENT_DIR . " is write able");
        }
    }
}
