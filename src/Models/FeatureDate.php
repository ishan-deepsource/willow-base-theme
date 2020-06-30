<?php

namespace Bonnier\Willow\Base\Models;

/**
 * @property int $post_id
 * @property  \WP_Post $post
 * @method static updateOrCreate(array $array, array $array1)
 * @method static where(string $string, string $string1, \Illuminate\Support\Carbon $now)
 */
class FeatureDate extends EloquentModel
{
    public $timestamps = false;
    protected $primaryKey = 'post_id';
    protected $fillable = ['post_id', 'timestamp'];
    protected $dates = ['timestamp'];

    public function getPostAttribute()
    {
        return get_post($this->post_id);
    }
}
