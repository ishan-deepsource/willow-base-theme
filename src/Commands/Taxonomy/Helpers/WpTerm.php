<?php

namespace Bonnier\Willow\Base\Commands\Taxonomy\Helpers;

use Bonnier\Willow\MuPlugins\Helpers\LanguageProvider;
use WP_CLI;

/**
 * Class WpTerm
 */
class WpTerm
{
    public static function create(
        $name,
        $slug,
        $languageCode,
        $contentHubId,
        $taxonomy,
        $parentTermId = null,
        $description,
        $meta
    ) {
        $createdTerm = wp_insert_term($name, $taxonomy, [
            'parent'      => $parentTermId,
            'slug'        => $slug,
            'description' => $description
        ]);

        if (is_wp_error($createdTerm)) {
            static::log(
                'warning',
                sprintf(
                    "Failed creating %s: %s Locale: %s content_hub_id: %s Errors: ",
                    $taxonomy,
                    $name,
                    $languageCode,
                    $contentHubId,
                    json_encode($createdTerm->errors, JSON_UNESCAPED_UNICODE)
                )
            );
            return null;
        }
        LanguageProvider::setTermLanguage($createdTerm['term_id'], $languageCode);
        update_term_meta($createdTerm['term_id'], 'content_hub_id', $contentHubId);

        collect($meta)->each(function ($value, $key) use ($createdTerm) {
            update_term_meta($createdTerm['term_id'], $key, $value);
        });

        static::log('success', "Created $taxonomy: $name Locale: $languageCode content_hub_id: $contentHubId");
        return $createdTerm['term_id'];
    }

    public static function update(
        $existingTermId,
        $name,
        $slug,
        $languageCode,
        $contentHubId,
        $taxonomy,
        $parentTermId = null,
        $description,
        $meta
    ) {
        $updatedTerm = wp_update_term($existingTermId, $taxonomy, [
            'name'        => $name,
            'parent'      => $parentTermId,
            'slug'        => $slug,
            'description' => $description
        ]);

        if (is_wp_error($updatedTerm)) {
            static::log('warning', "Failed updating $taxonomy: $name Locale: $languageCode content_hub_id: $contentHubId Errors: "
                . json_encode($updatedTerm->errors, JSON_UNESCAPED_UNICODE));
            return null;
        }
        LanguageProvider::setTermLanguage($existingTermId, $languageCode);
        update_term_meta($existingTermId, 'content_hub_id', $contentHubId);

        collect($meta)->each(function ($value, $key) use ($existingTermId) {
            update_term_meta($existingTermId, $key, $value);
        });

        static::log('success', "Updated $taxonomy: $name Locale: $languageCode content_hub_id: $contentHubId");
        return $existingTermId;
    }

    /**
     * @param $id
     *
     * @return null|string
     */
    public static function id_from_contenthub_id($id)
    {
        global $wpdb;
        return $wpdb->get_var(
            $wpdb->prepare("SELECT term_id FROM wp_termmeta WHERE meta_key=%s AND meta_value=%s", 'content_hub_id', $id)
        );
    }

    /**
     * @param $id
     *
     * @return null|string
     */
    public static function id_from_whitealbum_id($id)
    {
        global $wpdb;
        return $wpdb->get_var(
            $wpdb->prepare("SELECT term_id FROM wp_termmeta WHERE meta_key=%s AND meta_value=%s", 'whitealbum_id', $id)
        );
    }


    /**
     * @param $id
     *
     * @return null|string
     */
    public static function whiteablum_id($termId)
    {
        return get_term_meta($termId, 'whitealbum_id', true);
    }

    /*
    * @param $id integer wp_term->term_id
    *
    * @return null|string
    */
    public static function content_hub_id($id)
    {
        return get_term_meta($id, 'content_hub_id', true);
    }

    private static function log($type, $message)
    {
        if (class_exists('WP_CLI')) {
            WP_CLI::$type($message);
        }
    }
}
