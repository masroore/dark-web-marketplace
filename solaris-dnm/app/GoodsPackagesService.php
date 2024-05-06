<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\GoodsPackagesService.
 *
 * @property int $id
 * @property int $package_id
 * @property int $service_id
 * @property GoodsPackage $package
 * @property PaidService $service
 *
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackagesService whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackagesService wherePackageId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackagesService whereServiceId($value)
 *
 * @mixin \Eloquent
 */
class GoodsPackagesService extends Model
{
    protected $table = 'goods_packages_services';

    protected $primaryKey = 'id';

    public $fillable = [
        'package_id', 'service_id',
    ];

    public $timestamps = false;

    /**
     * @return GoodsPackage|\Illuminate\Database\Eloquent\Relations\BelongsTo|\Illuminate\Database\Query\Builder
     */
    public function package()
    {
        return $this->belongsTo('App\GoodsPackage', 'package_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo('App\PaidService', 'service_id', 'id');
    }
}
