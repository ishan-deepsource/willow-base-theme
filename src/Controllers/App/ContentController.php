<?php

namespace Bonnier\Willow\Base\Controllers\App;

use Bonnier\Willow\Base\Models\Contracts\Composites\CompositeContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;
use Bonnier\WP\ContentHub\Editor\Helpers\SortBy;

class ContentController extends BaseController
{
    public function register_routes()
    {
        register_rest_route('app/content', '/popular', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, 'popular']
        ]);
    }

    public function popular()
    {
        $composites = SortBy::getPopularComposites();

        if ($composites->isNotEmpty()) {
            $composites = $composites->map(function (CompositeContract $composite) {
                return with(new CompositeTeaserTransformer)->transform($composite);
            });
        }

        return new \WP_REST_Response(['data' => $composites->toArray()]);
    }
}
