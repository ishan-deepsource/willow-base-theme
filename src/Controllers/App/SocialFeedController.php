<?php

namespace Bonnier\Willow\Base\Controllers\App;

use Bonnier\WP\SoMe\Repositories\FacebookRepository;
use Bonnier\WP\SoMe\Repositories\PinterestRepository;
use Bonnier\WP\SoMe\SoMe;
use Bonnier\Willow\Base\Adapters\Wp\App\InstagramCompositeAdapter;
use Bonnier\Willow\Base\Adapters\Wp\App\PinterestCompositeAdapter;
use Bonnier\Willow\Base\Helpers\Cache;
use Bonnier\Willow\Base\Models\Base\Composites\Composite;
use Bonnier\Willow\Base\Traits\AuthenticationTrait;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;
use Bonnier\Willow\Base\Transformers\Pagination\StringCursor;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

class SocialFeedController extends WP_REST_Controller
{
    use AuthenticationTrait;

    protected $feedMapping = [
        'pinterest' => PinterestCompositeAdapter::class,
        'instagram' => InstagramCompositeAdapter::class
    ];
    
    public function register_routes()
    {
        register_rest_route('app', '/socialfeed', [
            'methods'  => WP_REST_Server::READABLE,
            'callback' => [$this, 'getSocialFeed']
        ]);
        register_rest_route('app', '/socialfeed/warm', [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'warmSocialFeed'],
            'permission_callback' => [$this, 'authenticate']
        ]);
    }
    
    public function getSocialFeed(WP_REST_Request $request)
    {
        if (!class_exists(SoMe::class)) {
            return new WP_REST_Response(['error' => 'Missing plugin!'], 400);
        }
        
        $offset = intval($request->get_param('cursor') ?: 0);

        $data = Cache::remember('social-feed-'.$offset, 1*HOUR_IN_SECONDS, function () use ($offset) {
            $rawFeed = SoMe::instance()->getSoMeRepo()->getFeed($offset, 8);
            
            $nextOffset = $rawFeed['offset'] ?? 0;
            
            $manager = new Manager();
            $resource = new Collection($this->formatFeed($rawFeed), new CompositeTeaserTransformer());
            $resource->setCursor($this->createCursor($offset, $nextOffset));
            
            return $manager->createData($resource)->toArray();
        });

        return new WP_REST_Response($data);
    }

    public function warmSocialFeed(WP_REST_Request $request)
    {
        if (!class_exists(SoMe::class)) {
            return new WP_REST_Response(['error' => 'Missing plugin!'], 400);
        }

        $facebookRepo = new FacebookRepository();
        $pinterestRepo = new PinterestRepository();
        switch ($request->get_param('provider')) {
            case 'facebook':
                $facebookRepo->storeInstagramPosts();
                break;
            case 'pinterest':
                $pinterestRepo->storePins();
                break;
            default:
                $facebookRepo->storeInstagramPosts();
                $pinterestRepo->storePins();
                break;
        }

        return new WP_REST_Response([
            'status' => 'success',
            'message' => 'Social Feed Storage was warmed!',
        ]);
    }
    
    private function formatFeed($rawFeed)
    {
        return collect($rawFeed['feed'])->map(function ($feeds, $source) {
            return collect($feeds)->map(function ($feed) use ($source) {
                $adapter = $this->feedMapping[$source];
                return new Composite(new $adapter($feed));
            });
        })->flatten();
    }
    
    private function createCursor($currentCursor, $nextCursor)
    {
        $cursor = new StringCursor();
        $cursor->setCurrent($currentCursor);
        if ($nextCursor) {
            $cursor->setNext($nextCursor);
        }
        
        return $cursor;
    }
}
