<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * App\OrdersService.
 *
 * @property int $id
 * @property int $order_id
 * @property string $title
 * @property float $price
 * @property string $currency
 * @property Order $order
 *
 * @method static \Illuminate\Database\Query\Builder|\App\OrdersService whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrdersService whereOrderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrdersService whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrdersService wherePrice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\OrdersService whereCurrency($value)
 *
 * @mixin \Eloquent
 *
 * @property float $price_btc
 *
 * @method static \Illuminate\Database\Query\Builder|\App\OrdersService wherePriceBtc($value)
 */
class OrdersService extends Model
{
    protected $table = 'orders_services';

    protected $primaryKey = 'id';

    public $timestamps = false;

    public $fillable = ['order_id', 'title', 'price', 'currency', 'price_btc'];

    /**
     * @return Builder|\Illuminate\Database\Eloquent\Relations\BelongsTo|Order
     */
    public function order()
    {
        return $this->belongsTo('App\Order', 'order_id', 'id');
    }
}
