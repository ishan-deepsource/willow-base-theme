<?php

namespace Bonnier\Willow\Base\Actions\Backend;

class PreviewUrl
{
    public function __construct()
    {
        add_filter('preview_post_link', [$this, 'appendNonceToPreviewUrl']);
        add_filter('preview_page_link', [$this, 'appendNonceToPreviewUrl']);
        add_filter('preview_post_link', [$this, 'appendNoCacheParamsToPreviewUrl']);
        add_filter('preview_page_link', [$this, 'appendNoCacheParamsToPreviewUrl']);
    }

    public function appendNonceToPreviewUrl($link)
    {
        // Needed to fetch draft content through rest api
        return sprintf('%s&nonce=%s', $link, wp_create_nonce('wp_rest'));
    }

    public function appendNoCacheParamsToPreviewUrl($link)
    {
        return sprintf('%s&cacheClear=true&uid=%s', $link, bin2hex(random_bytes(16)));
    }
}
