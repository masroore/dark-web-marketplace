<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\CustomPlace.
 *
 * @property int $id
 * @property int $good_id
 * @property int $region_id
 * @property string $title
 * @property Good $good
 * @property Region $region
 *
 * @method static \Illuminate\Database\Query\Builder|\App\CustomPlace whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CustomPlace whereGoodId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CustomPlace whereRegionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\CustomPlace whereTitle($value)
 *
 * @mixin \Eloquent
 *
 * @property int $shop_id
 * @property Shop $shop
 *
 * @method static \Illuminate\Database\Query\Builder|\App\CustomPlace whereShopId($value)
 */
class CustomPlace extends Model
{
    protected $table = 'custom_places';

    protected $primaryKey = 'id';

    protected $fillable = [
        'shop_id', 'good_id', 'region_id', 'title',
    ];

    public $timestamps = false;

    /**
     * @return Builder|\Illuminate\Database\Eloquent\Relations\BelongsTo|Shop
     */
    public function shop()
    {
        return $this->belongsTo('App\Shop', 'shop_id', 'id');
    }

    /**
     * @return Builder|Good|\Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function good()
    {
        return $this->belongsTo('App\Good', 'good_id', 'id');
    }

    /**
     * @return Builder|\Illuminate\Database\Eloquent\Relations\BelongsTo|Region
     */
    public function region()
    {
        return $this->belongsTo('App\Region', 'region_id', 'id');
    }

    public function delete()
    {
        GoodsPosition::where('custom_place_id', $this->id)->update([
            'subregion_id' => $this->region_id,
            'custom_place_id' => null,
        ]);

        return parent::delete();
    }
}
