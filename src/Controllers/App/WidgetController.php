<?php

namespace Bonnier\Willow\Base\Controllers\App;

use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types\TeaserListAdapter;
use Bonnier\Willow\Base\Models\Base\Pages\Contents\Types\TeaserList;
use Bonnier\Willow\Base\Transformers\Api\Root\Contents\Types\TeaserListTransformer;
use Bonnier\WP\ContentHub\Editor\Helpers\AcfName;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class WidgetController extends \WP_REST_Controller
{
    public function register_routes()
    {
        register_rest_route('app/widget', '/teaser-list', [
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, 'teaserList']
        ]);
    }

    public function teaserList(\WP_REST_Request $request)
    {
        $data = json_decode(base64_decode($request->get_param('cursor')));
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new \WP_REST_Response(null, 404);
        }

        $parentId = data_get($data, 'parent_id');
        $page = data_get($data, 'page');

        if (!$parentId || !$page) {
            return new \WP_REST_Response(null, 404);
        }

        if ($acfData = get_field(AcfName::GROUP_PAGE_WIDGETS, $parentId)) {
            $acfArray = collect($acfData)->first(function ($acfArray) {
                return $acfArray['acf_fc_layout'] == 'teaser_list' && ($acfArray['pagination'] ?? false);
            });
            if (empty($acfArray)) {
                return new \WP_REST_Response(null, 404);
            }
            $teaserList = new TeaserList(new TeaserListAdapter($acfArray));
            $teaserList->setParentId($parentId)
                ->setPage($page);

            $resource = new Item($teaserList, new TeaserListTransformer);

            return new \WP_REST_Response(with(new Manager)->createData($resource)->toArray());
        }
        return new \WP_REST_Response(null, 404);
    }
}
