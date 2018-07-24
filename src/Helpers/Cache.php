<?php

namespace Bonnier\Willow\Base\Helpers;

class Cache
{
    /**
     * @param string $key Cache key
     * @param int $expires_in Seconds
     * @param callable $callback
     *
     * @return mixed
     */
    public static function remember(string $key, int $expires_in, callable $callback)
    {
        if (($cached = wp_cache_get($key)) && !in_array(getenv('WP_ENV'), ['testing', 'development'])) {
            return unserialize($cached);
        }
        $data = $callback();
        wp_cache_set($key, serialize($data), '', $expires_in);
        return $data;
    }
    
    /**
     * @param string $key
     * @param callable $callback
     *
     * @return mixed
     */
    public static function rememberForever(string $key, callable $callback)
    {
        return self::remember($key, 0, $callback);
    }
}
