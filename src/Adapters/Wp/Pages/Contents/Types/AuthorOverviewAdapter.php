<?php

namespace Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\Types;

use Bonnier\Willow\Base\Adapters\Wp\Pages\Contents\AbstractContentAdapter;
use Bonnier\Willow\Base\Models\ACF\User\UserFieldGroup;
use Bonnier\Willow\Base\Models\Contracts\Pages\Contents\Types\AuthorOverviewContract;
use Illuminate\Support\Collection;

class AuthorOverviewAdapter extends AbstractContentAdapter implements AuthorOverviewContract
{
    public function __construct(array $acfArray)
    {
        parent::__construct($acfArray);
    }

    public function getEditorsDescriptionTitle(): ?string
    {
        return array_get($this->acfArray, 'editors_description_title') ?: null;
    }

    public function getEditorsDescription(): ?string
    {
        return array_get($this->acfArray, 'editors_description') ?: null;
    }


    public function getAuthors(): Collection
    {
         $authorIds = array_map(function (array $author) {
            return $author['ID'];
         }, $this->acfArray['authors']);

         $args = [
             'meta_query' => [
                  [
                      'key' => UserFieldGroup::PUBLIC_FIELD,
                      'value' => true,
                      'compare' => '=='
                  ]
             ],
             'include' => $authorIds,
         ];

        $users = collect(get_users($args));

        // Sort the users so the order selected matters.
        $sortedUsers = $users->sortBy(function (\WP_User $user) use ($authorIds) {
            return array_search($user->ID, $authorIds);
        })->all();

         return collect($sortedUsers);
    }
}
