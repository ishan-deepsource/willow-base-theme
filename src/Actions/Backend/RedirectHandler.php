<?php


namespace Bonnier\Willow\Base\Actions\Backend;


use Bonnier\WP\Redirect\WpBonnierRedirect;

class RedirectHandler
{
    public function __construct()
    {
        add_filter(WpBonnierRedirect::FILTER_SLUG_IS_LIVE, [$this, 'validateSlug'], 10, 4);
    }

    /**
     * @param bool $isLive
     * @param string $url
     * @param string $locale
     * @param \WP_Post|\WP_Term|null $object
     *
     * @return bool
     */
    public function validateSlug(bool $isLive, string $url, string $locale, $object): bool {
        if ($isLive && $object instanceof \WP_Post) {
            $platforms = get_field('exclude_platforms', $object->ID);
            if (is_array($platforms) && in_array('web', $platforms)) {
                return false;
            }
        }
        return $isLive;
    }
}
