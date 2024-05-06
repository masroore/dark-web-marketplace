<?php

namespace App;

use App\Packages\PriceModifier\IPriceModifier;
use App\Packages\PriceModifier\PriceModifierService;
use App\Packages\Utils\BitcoinUtils;
use App\Packages\Utils\Formatters;
use Config;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\GoodsPackage.
 *
 * @property int $id
 * @property int $shop_id
 * @property int $good_id
 * @property float $amount
 * @property string $measure
 * @property string $currency
 * @property int $net_cost
 * @property bool $preorder
 * @property int $preorder_time
 *
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackage whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackage whereShopId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackage whereGoodId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackage whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackage whereMeasure($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackage whereCurrency($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackage wherePreorder($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackage wherePreorderTime($value)
 *
 * @mixin \Eloquent
 *
 * @property float $price
 *
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackage wherePrice($value)
 *
 * @property Good $good
 * @property \App\GoodsPosition[]|\Illuminate\Database\Eloquent\Collection $positions
 * @property \App\PaidService[]|\Illuminate\Database\Eloquent\Collection $services
 * @property float $employee_reward
 *
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackage whereEmployeeReward($value)
 *
 * @property float $employee_penalty
 *
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackage whereEmployeePenalty($value)
 *
 * @property \App\GoodsPackagesService[]|\Illuminate\Database\Eloquent\Collection $packageServices
 * @property float $qiwi_price
 *
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackage whereQiwiPrice($value)
 *
 * @property bool $qiwi_enabled
 *
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackage whereQiwiEnabled($value)
 *
 * @property int $city_id
 *
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackage whereCityId($value)
 *
 * @property City $city
 *
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackage whereNetCost($value)
 *
 * @property bool $has_quests
 * @property bool $has_ready_quests
 *
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackage filterRegion($region)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackage whereHasQuests($value)
 * @method static \Illuminate\Database\Query\Builder|\App\GoodsPackage whereHasReadyQuests($value)
 */
class GoodsPackage extends Model
{
    public const MEASURE_GRAM = 'gr';
    public const MEASURE_PIECE = 'piece';
    public const MEASURE_ML = 'ml';

    public const PREORDER_TIME_STEPS = [24, 48, 72, 480];
    public const PREORDER_TIME_24 = '24';
    public const PREORDER_TIME_48 = '48';
    public const PREORDER_TIME_72 = '72';
    public const PREORDER_TIME_480 = '480';

    protected $table = 'goods_packages';

    protected $primaryKey = 'id';

    protected $fillable = [
        'shop_id', 'good_id', 'city_id',
        'amount', 'measure',
        'price', 'currency',
        'net_cost',
        'qiwi_enabled', 'qiwi_price',
        'preorder', 'preorder_time',
        'employee_reward', 'employee_penalty',
        'has_quests', 'has_ready_quests',
    ];

    protected $casts = [
        'amount' => 'float',
        'price' => 'float',
        'employee_reward' => 'float',
        'employee_penalty' => 'float',
    ];

    public $timestamps = false;

    /**
     * @return BelongsTo|Good
     */
    public function good()
    {
        return $this->belongsTo('App\Good', 'good_id', 'id');
    }

    /** @return BelongsTo|City */
    public function city()
    {
        return $this->belongsTo('App\City', 'city_id', 'id');
    }

    public function positions()
    {
        return $this->hasMany('App\GoodsPosition', 'package_id', 'id');
    }

    public function availablePositions()
    {
        return $this->positions()->where(function ($query): void {
            $query->where('available', 1);
        });
    }

    public function notModeratedPositions()
    {
        return $this->positions()->where('available', 0)->where('moderated', 0);
    }

    public function packageServices()
    {
        return $this->hasMany('App\GoodsPackagesService', 'package_id', 'id');
    }

    // use packageServices for relationship
    // this method returns package services with info from `paid_services`
    public function services()
    {
        /*
        SELECT `paid_services`.*, `goods_packages_services`.`service_id` FROM `paid_services`
        inner join `goods_packages_services` on `goods_packages_services`.`service_id` = `paid_services`.`id`
        WHERE `goods_packages_services`.`package_id` = '?';
        */

        return DB::table('paid_services')
            ->join('goods_packages_services', 'goods_packages_services.service_id', '=', 'paid_services.id')
            ->where('goods_packages_services.package_id', $this->id)
            ->select('paid_services.*')
            ->get();
    }

    public function delete()
    {
        $this->availablePositions()->delete();

        return parent::delete();
    }

    /**
     * @return string
     */
    public function getHumanWeight()
    {
        return Formatters::getHumanWeight($this->amount, $this->measure);
    }

    /**
     * @param null|string $currency
     * @param IPriceModifier[] $modifiers
     * @param array $arguments
     *
     * @return float
     */
    public function getPrice($currency = null, $modifiers = [], $arguments = [])
    {
        /** @var PriceModifierService $priceModifiers */
        $priceModifiers = app('price_modifier');
        $resultPrice = $priceModifiers->apply($this->price, $this->currency, $modifiers, $arguments);

        return BitcoinUtils::convert($resultPrice, $this->currency, $currency ?: $this->currency);
    }

    /**
     * @param null|string $currency
     * @param IPriceModifier[] $modifiers
     * @param array $arguments
     *
     * @return string
     */
    public function getHumanPrice($currency = null, $modifiers = [], $arguments = [])
    {
        $currency = $currency ?: $this->currency;

        return human_price($this->getPrice($currency, $modifiers, $arguments), $currency);
    }

    /**
     * @param null $currency
     * @param IPriceModifier[] $modifiers
     * @param array $arguments
     *
     * @return float
     */
    public function getQiwiPrice($currency = null, $modifiers = [], $arguments = [])
    {
        if (!$this->qiwi_price) {
            return $this->getPrice($currency, $modifiers, $arguments);
        }
        $priceModifiers = app('price_modifier');
        $resultPrice = $priceModifiers->apply($this->qiwi_price, BitcoinUtils::CURRENCY_RUB, $modifiers, $arguments);

        return BitcoinUtils::convert($resultPrice, BitcoinUtils::CURRENCY_RUB, $currency ?: BitcoinUtils::CURRENCY_RUB);
    }

    /**
     * @param null $currency
     * @param IPriceModifier[] $modifiers
     * @param array $arguments
     *
     * @return string
     */
    public function getHumanQiwiPrice($currency = null, $modifiers = [], $arguments = [])
    {
        $currency = $currency ?: BitcoinUtils::CURRENCY_RUB;

        return human_price($this->getQiwiPrice($currency, $modifiers, $arguments), $currency);
    }

    /**
     * @param null|string $currency
     *
     * @return float
     */
    public function getGuaranteeFee($currency = null)
    {
        return BitcoinUtils::convert($this->price * Config::get('mm2.guarantee_fee'), $this->currency, $currency ?: $this->currency);
    }

    /**
     * @param null|string $currency
     *
     * @return float
     */
    public function getPriceWithGuaranteeFee($currency = null)
    {
        return $this->getPrice($currency) + $this->getGuaranteeFee($currency);
    }

    /**
     * @param null|string $currency
     *
     * @return string
     */
    public function getHumanPriceWithGuaranteeFee($currency = null)
    {
        $currency = $currency ?: $this->currency;

        return human_price($this->getPriceWithGuaranteeFee($currency), $currency);
    }

    /**
     * @return string
     */
    public function getAvailablePositionsCount()
    {
        if ($this->preorder) {
            return '(предзаказ)';
        }

        return $this->availablePositions()->count();
    }

    public function getNotModeratedPositionsCount()
    {
        if ($this->preorder) {
            return '(предзаказ)';
        }

        return $this->notModeratedPositions()->count();
    }

    public function scopeFilterRegion($query, $region)
    {
        return $query->whereHas('availablePositions', function ($query) use ($region) {
            return $query
                ->where('subregion_id', $region)
                ->orWhereIn('custom_place_id', CustomPlace::where('region_id', $region)->pluck('id')->toArray());
        });
    }
}
