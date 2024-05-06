<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Region.
 *
 * @property int $id
 * @property int $city_id
 * @property int $parent_id
 * @property string $title
 * @property int $priority
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Region whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Region whereCityId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Region whereParentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Region whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Region wherePriority($value)
 *
 * @mixin \Eloquent
 */
class Region extends Model
{
    protected $table = 'regions';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'city_id', 'parent_id', 'title', 'priority',
    ];
}
