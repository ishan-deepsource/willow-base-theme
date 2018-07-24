<?php

namespace Bonnier\Willow\Base\Actions\Frontend;

use WP_Post;

/**
 * Class Init
 *
 * @package \Bonnier\Willow\Base\ActionsFilters
 */
class Init
{

    /**
     * Init constructor.
     */
    public function __construct()
    {
        $this->printStatusHeader();
        $this->enableDebugBar();
    }

    private function printStatusHeader()
    {
        $status = 200;
        $wpObject = get_queried_object_json();
        if (!$wpObject || $wpObject instanceof WP_Post && get_option('404page_page_id', 0) !== $wpObject->ID) {
            $status = 404;
        }
        status_header($status);
    }

    private function enableDebugBar()
    {
        add_filter('debug_bar_enable', function ($status) {
            return getenv('WP_DEBUG') ?? false;
        });
    }
}
