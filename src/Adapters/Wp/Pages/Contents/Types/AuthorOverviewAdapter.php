<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\AuthorOverviewContract;
use Illuminate\Support\Collection;

class AuthorOverviewAdapter extends AbstractContentAdapter implements AuthorOverviewContract
{
    public function __construct(array $acfArray)
    {
        parent::__construct($acfArray);
        $this->page = 1;
    }

    public function getTitle(): ?string
    {
        return array_get($this->acfArray, 'title') ?: null;
    }

    public function getLabel(): ?string
    {
        return array_get($this->acfArray, 'label') ?: null;
    }

    public function getDescription(): ?string
    {
        return array_get($this->acfArray, 'description') ?: null;
    }

    public function getAuthors(): Collection
    {
         $authorIds = array_map(function (array $author) {
            return $author['ID'];
         }, $this->acfArray['authors']);

         $args = [
             'meta_query' => [
                  [
                      'key' => 'Public',
                      'value' => true,
                      'compare' => '=='
                  ]
             ],
             'include' => $authorIds,
         ];

         return collect(get_users($args));
    }
}
