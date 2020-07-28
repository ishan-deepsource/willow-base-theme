<?php

namespace Bonnier\Willow\Base\Helpers;

use Illuminate\Support\Collection;

/**
 * Class Collection
 */
class CollectionHelper
{
    public static function register()
    {
        Collection::macro('toAssocCombine', function () {
            return $this->reduce(function ($assoc, $taxonomyValue) {
                collect($taxonomyValue)->each(function ($value, $taxonomy) use (&$assoc) {
                    if (!isset($assoc[$taxonomy]) || !$assoc[$taxonomy] instanceof Collection) {
                        $assoc[$taxonomy] = new static();
                    }
                    $assoc[$taxonomy]->push($value);
                });
                return $assoc;
            }, new static());
        });
        Collection::macro('toAssoc', function () {
            return $this->reduce(function ($assoc, $keyValuePair) {
                list($key, $value) = $keyValuePair;
                $assoc[$key] = $value;
                return $assoc;
            }, new static());
        });
        Collection::macro('rejectNullValues', function () {
            return $this->reject(function ($value) {
                return is_null($value);
            });
        });
        Collection::macro('itemsToObject', function () {
            return collect(json_decode($this->toJson()));
        });
    }
}
