<?php

namespace Bonnier\Willow\Base\Controllers\App;

use Bonnier\Willow\MuPlugins\LanguageProvider;
use Bonnier\WP\Cxense\Parsers\Document;
use Bonnier\Willow\Base\Adapters\Cxense\Search\DocumentAdapter;
use Bonnier\Willow\Base\Adapters\Cxense\Search\FacetCollectionAdapter;
use Bonnier\Willow\Base\Helpers\Cache;
use Bonnier\Willow\Base\Repositories\CxenseSearchRepository;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;
use Bonnier\Willow\Base\Transformers\Api\Composites\TeaserTransformer;
use Bonnier\Willow\Base\Transformers\Api\Search\FacetCollectionTransformer;
use Bonnier\Willow\Base\Transformers\Pagination\NumberedPagination;
use League\Fractal\Manager;
use League\Fractal\Pagination\Cursor;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;

class SearchController extends WP_REST_Controller
{
    /* @var CxenseSearchRepository */
    protected $searchRepository;

    public function register_routes()
    {
        register_rest_route('app', '/search', [
            'methods'  => \WP_REST_Server::CREATABLE,
            'callback' => [$this, 'getSearchResults']
        ]);
        $this->searchRepository = new CxenseSearchRepository();
    }

    /**
     * @param \WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function getSearchResults(WP_REST_Request $request)
    {
        $locale = LanguageProvider::getCurrentLanguage();

        $filters = json_decode($request->get_body());

        if (!$filters) {
            return new WP_REST_Response([
                'code' => 'unprocessable_entity',
                'message' => 'No request body was submitted',
                'data' => [
                    'status' => 422
                ]
            ], 422);
        }

        $response = Cache::remember(
            'willow_search_' . $locale,
            4 * HOUR_IN_SECONDS,
            function () use ($locale, $filters) {
                $searchResults = $this->searchRepository->getSearchResults(
                    $filters->query,
                    $filters->page,
                    $filters->per_page,
                    (array)$filters->facets
                );
                $manager = new Manager();
                $resource = new Collection(
                    $this->formatSearchResults($searchResults->matches),
                    new CompositeTeaserTransformer()
                );
                $resource->setPaginator(
                    new NumberedPagination($filters->page, $filters->per_page, $searchResults->totalCount)
                );

                $resource->setMeta([
                    'facets' => $this->formatFacets($searchResults->facets)
                ]);

                return $manager->createData($resource)->toArray();
            }
        );

        return new WP_REST_Response($response);
    }

    private function formatSearchResults($matches)
    {
        return collect($matches)->map(function (Document $document) {
            return new DocumentAdapter($document);
        })->toArray();
    }

    private function formatFacets($facets)
    {
        return collect($facets)->map(function ($facetCollection) {
            return with(new FacetCollectionTransformer())->transform(new FacetCollectionAdapter($facetCollection));
        })->toArray();
    }
}
