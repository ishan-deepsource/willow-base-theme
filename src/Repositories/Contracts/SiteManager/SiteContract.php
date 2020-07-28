<?php

namespace Bonnier\Willow\Base\Repositories\Contracts\SiteManager;

interface SiteContract
{
    public static function get_all();
    public static function find_by_id($id);
}
