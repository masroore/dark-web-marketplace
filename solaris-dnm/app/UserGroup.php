<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\UserGroup.
 *
 * @property int $id
 * @property string $title
 * @property string $mode
 * @property float $percent_amount
 * @property int $buy_count
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \App\User[]|\Illuminate\Database\Eloquent\Collection $users
 *
 * @method static \Illuminate\Database\Query\Builder|\App\UserGroup whereBuyCount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserGroup whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserGroup whereMode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserGroup wherePercentAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserGroup whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\UserGroup whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class UserGroup extends Model
{
    public const MODE_MANUAL = 'manual';
    public const MODE_AUTO = 'auto';

    protected $table = 'users_groups';

    protected $primaryKey = 'id';

    protected $fillable = [
        'title', 'mode', 'percent_amount', 'buy_count',
    ];

    // - Dependencies

    /**
     * @return \App\User[]|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('App\User', 'group_id', 'id');
    }

    // - Methods

    public function getHumanDiscount()
    {
        return trim_zeros(number_format($this->percent_amount, 2)) . ' %';
    }
}
