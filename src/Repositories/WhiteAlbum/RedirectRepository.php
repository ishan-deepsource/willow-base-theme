<?php

namespace Bonnier\Willow\Base\Repositories\WhiteAlbum;

use GuzzleHttp\Client;

/**
 * Class RedirectRepository
 *
 * @package \Bonnier\Willow\Base\Repositories\WhiteAlbum
 */
class RedirectRepository
{
    const WA_ROUTE_RESOLVES_TABLE = 'whitealbum_route_resolves';
    const WA_ROUTE_RESOLVES_TABLE_CREATED = 'whitealbum_route_resolves_created';
    const WA_ROUTE_RESOLVES_TABLE_VERSION = 1;

    protected $client;
    protected $locale;

    /**
     * RedirectRepository constructor.
     *
     * @param string $domain
     * @param string $locale
     */
    public function __construct(string $domain, string $locale)
    {
        $this->locale = $locale;
        $this->client = new Client([
            'base_uri' => sprintf('http://old.%s', $domain),
        ]);
        $this->createRouteResolvesTable();
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    public function findWaRedirect($path)
    {
        if ($existingRedirect = $this->findRelsovedRedirectInDb($path)) {
            if ($existingRedirect->code = 404) {
                return null;
            }
            return $existingRedirect->to;
        }
        $redirectUrl = $this->recursiveRedirectResolve($path);
        $redirectPath = parse_url($redirectUrl, PHP_URL_PATH);
        if ($redirectPath && $redirectPath !== $path) {
            return $redirectUrl;
        }
    }

    private function recursiveRedirectResolve($url, $originalUrl = null)
    {
        try {
            $response = $this->client->head($url);
        } catch (\Exception $exception) {
            $response = $exception->getResponse();
        }
        if ($location = $response->getHeader('Location')) {
            return $this->recursiveRedirectResolve($location, $originalUrl ?: $url);
        }
        $this->storeResolvedRedirect($originalUrl ?: $url, $url, $response->getStatusCode());
        return $url;
    }

    public function createRouteResolvesTable()
    {
        if (get_option(static::WA_ROUTE_RESOLVES_TABLE_CREATED) === static::WA_ROUTE_RESOLVES_TABLE_VERSION) {
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . static::WA_ROUTE_RESOLVES_TABLE;
        $charset = $wpdb->get_charset_collate();

        $sql = "SET sql_notes = 1;
            CREATE TABLE `$table` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `from` text CHARACTER SET utf8 NOT NULL,
              `from_hash` char(32) COLLATE utf8mb4_unicode_520_ci NOT NULL,
              `to` text CHARACTER SET utf8 NOT NULL,
              `to_hash` char(32) COLLATE utf8mb4_unicode_520_ci NOT NULL,
              `locale` varchar(2) CHARACTER SET utf8 NOT NULL,
              `code` int(3) DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `from_hash` (`from_hash`,`to_hash`,`locale`),
              KEY `from_hash_2` (`from_hash`,`to_hash`,`locale`),
              KEY `from_hash_3` (`from_hash`)
            ) $charset;
            SET sql_notes = 1;
            ";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        update_option(static::WA_ROUTE_RESOLVES_TABLE_CREATED, static::WA_ROUTE_RESOLVES_TABLE_VERSION, true);
    }

    /**
     * @param $path
     *
     * @return array|null|object|void
     */
    private function findRelsovedRedirectInDb($path)
    {
        global $wpdb;
        $table = $wpdb->prefix . static::WA_ROUTE_RESOLVES_TABLE;
        try {
            return $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM `$table` WHERE `from_hash` = MD5(%s) AND `locale` = %s",
                    $path,
                    $this->locale
                )
            );
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param $fromUrl
     * @param $toUrl
     * @param $code
     *
     * @return mixed
     */
    private function storeResolvedRedirect($fromUrl, $toUrl, $code)
    {
        global $wpdb;
        $fromUrl = parse_url($fromUrl, PHP_URL_PATH);
        $table = $wpdb->prefix . static::WA_ROUTE_RESOLVES_TABLE;
        try {
            return $wpdb->query(
                $wpdb->prepare(
                    "INSERT INTO `$table` 
                    (`from`, `from_hash`, `to`, `to_hash`, `locale`, `code`) 
                    VALUES (%s, MD5(%s), %s, MD5(%s), %s, %d)",
                    [
                        $fromUrl,
                        $fromUrl,
                        $toUrl,
                        $toUrl,
                        $this->locale,
                        $code
                    ]
                )
            );
        } catch (\Exception $e) {
            return null;
        }
    }
}
