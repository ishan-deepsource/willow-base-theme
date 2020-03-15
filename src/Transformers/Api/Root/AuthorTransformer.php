<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use Bonnier\Willow\Base\Transformers\Api\Composites\CompositeTeaserTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\Partials\AuthorDetailsTransformer;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

/**
 * Class AuhtorTransformer
 *
 * @package \Bonnier\Willow\Base\Transformers\Api\Composites\Partials
 */
class AuthorTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'details',
        'content-teasers'
    ];

    public function transform(AuthorContract $author)
    {
        return [
            'id' => $author->getId(),
            'name' => $author->getName(),
            'title' => $author->getTitle(),
            'biography' => $author->getBiography(),
            'avatar' => $this->getAvatar($author),
            'url' => $author->getUrl(),
            'public' => $author->isPublic(),
            'count' => $author->getCount(),
        ];
    }

    public function includeDetails(AuthorContract $author)
    {
        if ($author->isPublic()) {
            return $this->item($author, new AuthorDetailsTransformer());
        }
        return null;
    }

    public function includeContentTeasers(AuthorContract $author, ParamBag $paramBag)
    {
        list($perPage) = $paramBag->get('per_page') ?: [10];
        list($page) = $paramBag->get('page') ?: [1];
        list($orderby) = $paramBag->get('orderby') ?: ['date'];
        list($order) = $paramBag->get('order') ?: ['DESC'];
        list($offset) = $paramBag->get('offset') ?: ['0'];

        return $this->collection(
            $author->getContentTeasers($page, $perPage, $orderby, $order, $offset),
            new CompositeTeaserTransformer()
        );
    }

    private function getAvatar(AuthorContract $author)
    {
        $avatar = $author->getAvatar();
        return $avatar ? with(new ImageTransformer())->transform($avatar) : null;
    }
}
