<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root;

use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use League\Fractal\TransformerAbstract;

/**
 * Class AuhtorTransformer
 *
 * @package \Bonnier\Willow\Base\Transformers\Api\Composites\Partials
 */
class AuthorTransformer extends TransformerAbstract
{
    public function transform(AuthorContract $author)
    {
        return [
            'id' => $author->getId(),
            'name' => $author->getName(),
            'title' => $author->getTitle(),
            'biography' => $author->getBiography(),
            'avatar' => $this->getAvatar($author),
            'url' => $author->getUrl()
        ];
    }

    private function getAvatar(AuthorContract $author)
    {
        $avatar = $author->getAvatar();
        return $avatar ? with(new ImageTransformer())->transform($avatar) : null;
    }
}
