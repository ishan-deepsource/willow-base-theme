<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root\Partials;

use Bonnier\Willow\Base\Models\Contracts\Root\AuthorContract;
use League\Fractal\TransformerAbstract;

class AuthorDetailsTransformer extends TransformerAbstract
{
    public function transform(AuthorContract $author)
    {
        return [
            'website' => $author->getWebsite(),
            'email' => $author->getEmail(),
            'birthday' => $author->getBirthday()
        ];
    }
}