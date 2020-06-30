<?php

namespace Bonnier\Willow\Base\Repositories\Contracts\SiteManager;

interface TaxonomyContract
{
    public static function get_all($page = 1);

    public static function find_by_id($id);

    public static function find_by_content_hub_id($id);

    public static function find_by_brand_id($id, $page = 1);
}
