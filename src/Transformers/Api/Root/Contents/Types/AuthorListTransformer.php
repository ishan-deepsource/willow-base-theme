<?php

namespace Bonnier\Willow\Base\Transformers\Api\Root\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Root\AuthorAdapter;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\AuthorOverviewContract;
use Bonnier\Willow\Base\Transformers\Api\Root\AuthorTransformer;
use Bonnier\Willow\Base\Transformers\Api\Root\HyperlinkTransformer;
use League\Fractal\TransformerAbstract;

class AuthorListTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'authors',
    ];


    public function transform(AuthorOverviewContract $authorOverview)
    {
        return [
            'title' => $authorOverview->getTitle(),
            'label' => $authorOverview->getLabel(),
            'description' => $authorOverview->getDescription(),
        ];
    }

    private function transformLink(AuthorOverviewContract $teaserList)
    {
        if (optional($teaserList->getLink())->getUrl()) {
            return with(new HyperlinkTransformer)->transform($teaserList->getLink());
        }

        return null;
    }

    public function includeAuthors(AuthorOverviewContract $authorOverview)
    {
        $authors = $authorOverview->getAuthors()->map(function (\WP_User $author) {
            return new AuthorAdapter($author);
        })->all();

        return $this->collection($authors, new AuthorTransformer());
    }
}
