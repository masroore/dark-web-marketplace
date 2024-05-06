<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * App\GoodsReview.
 *
 * @property int $id
 * @property int $good_id
 * @property int $user_id
 * @property int $order_id
 * @property string $text
 * @property int $shop_rating
 * @property int $dropman_rating
 * @property int $item_rating
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Good $good
 * @property Order $order
 * @property User $user
 *
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsReview whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsReview whereGoodId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsReview whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsReview whereOrderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsReview whereText($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsReview whereShopRating($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsReview whereDropmanRating($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsReview whereItemRating($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsReview whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsReview whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 *
 * @property string $reply_text
 * @property bool $hidden
 *
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsReview whereReplyText($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsReview whereHidden($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsReview applySearchFilters(\Illuminate\Http\Request $request)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsReview filterUser($userId)
 *
 * @property int $city_id
 * @property City $city
 *
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsReview whereCityId($value)
 */
class GoodsReview extends Model
{
    protected $table = 'goods_reviews';

    protected $primaryKey = 'id';

    protected $fillable = [
        'good_id', 'user_id', 'order_id', 'city_id', 'text', 'shop_rating', 'dropman_rating', 'item_rating',
    ];

    public $timestamps = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function good()
    {
        return $this->belongsTo('App\Good', 'good_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo('App\Order', 'order_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo('App\City', 'city_id', 'id');
    }

    public function scopeApplySearchFilters(\Illuminate\Database\Eloquent\Builder $reviews, Request $request)
    {
        if (!empty($user = $request->get('user'))) {
            $reviews = $reviews->filterUser($user);
        }

        return $reviews;
    }

    public function scopeFilterUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function getAverageRating()
    {
        return ($this->shop_rating + $this->dropman_rating + $this->item_rating) / 3;
    }

    public function getLastEditTime()
    {
        return Carbon::now()->diffInMinutes($this->updated_at, true);
    }

    public function getEditRemainingTime()
    {
        return config('mm2.review_edit_time') * 3600 - Carbon::now()->diffInSeconds($this->created_at, true);
    }
}
