<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Page.
 *
 * @property int $id
 * @property int $shop_id
 * @property string $title
 * @property string $body
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property Shop $shop
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Page whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Page whereShopId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Page whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Page whereBody($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Page whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Page whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Page extends Model
{
    protected $table = 'pages';

    protected $primaryKey = 'id';

    protected $fillable = ['shop_id', 'title', 'body'];

    /**
     * @return Builder|\Illuminate\Database\Eloquent\Relations\BelongsTo|Shop
     */
    public function shop()
    {
        return $this->belongsTo('App\Shop', 'shop_id', 'id');
    }
}
