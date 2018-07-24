<?php

use Bonnier\Willow\Base\Bootstrap;
use Illuminate\Support\Debug\Dumper;

add_action('init', function () {
    new Bootstrap();
});

remove_action('template_redirect', 'redirect_canonical');

// Redirect all requests to index.php so the Vue app is loaded and 404s aren't thrown
function remove_redirects()
{
    add_rewrite_rule('^/(.+)/?', 'index.php', 'top');
}
add_action('init', 'remove_redirects');

function get_queried_object_json()
{
    if ($object = get_queried_object()) {
        $object->wp_type = strtolower(class_basename($object));
        $object->template = get_page_template_slug($object->ID);
        return json_encode($object);
    }
    return json_encode(null);
}

function ddHtml(...$args)
{
    header('Content-Type: text/html');
    foreach ($args as $x) {
        (new Dumper)->dump($x);
    }

    die(1);
}
