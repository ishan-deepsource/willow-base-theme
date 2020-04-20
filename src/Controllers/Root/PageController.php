<?php

namespace Bonnier\Willow\Base\Controllers\Root;

class PageController extends \WP_REST_Posts_Controller
{
    public function check_read_permission($post)
    {
        return true;
    }
}
