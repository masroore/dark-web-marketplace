<?php

namespace App;

use App\Packages\Utils\BitcoinUtils;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * App\Employee.
 *
 * @property int $id
 * @property int $shop_id
 * @property int $user_id
 * @property string $description
 * @property string $role
 * @property Shop $shop
 * @property User $user
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereShopId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereRole($value)
 *
 * @mixin \Eloquent
 *
 * @property int $city_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool $goods_create
 * @property bool $goods_edit
 * @property bool $goods_delete
 * @property bool $goods_only_own_city
 * @property \App\GoodsPosition[]|\Illuminate\Database\Eloquent\Collection $positions
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereCityId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereGoodsCreate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereGoodsEdit($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereGoodsDelete($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereGoodsOnlyOwnCity($value)
 *
 * @property bool $quests_create
 * @property bool $quests_edit
 * @property bool $quests_delete
 * @property bool $quests_only_own_city
 * @property array $quests_allowed_goods
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereQuestsCreate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereQuestsEdit($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereQuestsDelete($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereQuestsOnlyOwnCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereQuestsAllowedGoods($value)
 *
 * @property bool $sections_messages
 * @property bool $sections_appearance
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereSectionsMessages($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereSectionsAppearance($value)
 *
 * @property bool $sections_orders
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereSectionsOrders($value)
 *
 * @property string $note
 * @property City $city
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereNote($value)
 *
 * @property bool $sections_paid_services
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereSectionsPaidServices($value)
 *
 * @property bool $sections_finances
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereSectionsFinances($value)
 *
 * @property bool $sections_settings
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereSectionsSettings($value)
 *
 * @property bool $sections_pages
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereSectionsPages($value)
 *
 * @property float $balance
 * @property \App\EmployeesEarning[]|\Illuminate\Database\Eloquent\Collection $earnings
 * @property \App\EmployeesPayout[]|\Illuminate\Database\Eloquent\Collection $payouts
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereBalance($value)
 *
 * @property bool $sections_stats
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereSectionsStats($value)
 *
 * @property bool $quests_not_only_own
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereQuestsNotOnlyOwn($value)
 *
 * @property bool $sections_qiwi
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereSectionsQiwi($value)
 *
 * @property bool $quests_autojoin
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereQuestsAutojoin($value)
 *
 * @property bool $quests_preorders
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereQuestsPreorders($value)
 *
 * @property bool $sections_employees
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereSectionsEmployees($value)
 *
 * @property bool $sections_messages_private
 * @property string $sections_messages_private_description
 * @property bool $sections_messages_private_autojoin
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereSectionsMessagesPrivate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereSectionsMessagesPrivateAutojoin($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereSectionsMessagesPrivateDescription($value)
 *
 * @property bool $sections_discounts
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereSectionsDiscounts($value)
 *
 * @property bool $sections_own_orders
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereSectionsOwnOrders($value)
 *
 * @property bool $quests_moderate
 * @property bool $sections_moderate
 *
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereQuestsModerate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Employee whereSectionsModerate($value)
 */
class Employee extends Model
{
    public const ROLE_OWNER = 'owner';
    public const ROLE_SUPPORT = 'support';
    public const ROLE_DEADDROP = 'deaddrop';

    protected $table = 'employees';

    protected $primaryKey = 'id';

    protected $casts = [
        'goods_create' => 'boolean',
        'goods_edit' => 'boolean',
        'goods_delete' => 'boolean',
        'goods_only_own_city' => 'boolean',
        'quests_create' => 'boolean',
        'quests_edit' => 'boolean',
        'quests_delete' => 'boolean',
        'quests_only_own_city' => 'boolean',
        'quests_not_only_own' => 'boolean',
        'quests_autojoin' => 'boolean',
        'quests_allowed_goods' => 'array',
        'quests_moderate' => 'boolean',
        'sections_messages' => 'boolean',
        'sections_appearance' => 'boolean',
        'sections_pages' => 'boolean',
        'sections_qiwi' => 'boolean',
        'sections_discounts' => 'boolean',
        'sections_own_orders' => 'boolean',
        'sections_moderate' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shop_id', 'user_id', 'city_id', 'description', 'note', 'role',
    ];

    /**
     * @return string
     */
    public static function getHumanRole($role)
    {
        switch ($role) {
            case self::ROLE_OWNER:
                return 'Владелец магазина';

            case self::ROLE_SUPPORT:
                return 'Поддержка';

            case self::ROLE_DEADDROP:
                return 'Кладмен';

            default:
                throw new InvalidArgumentException('Unknown role type.');
        }
    }

    /**
     * @return Builder|\Illuminate\Database\Eloquent\Relations\BelongsTo|Shop
     */
    public function shop()
    {
        return $this->belongsTo('App\Shop', 'shop_id', 'id');
    }

    /**
     * @return Builder|\Illuminate\Database\Eloquent\Relations\BelongsTo|User
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    /**
     * @return Builder|City|\Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo('App\City', 'city_id', 'id');
    }

    /**
     * @return Builder|EmployeesEarning|\Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function earnings()
    {
        return $this->hasMany('App\EmployeesEarning', 'employee_id', 'id');
    }

    /**
     * @return Builder|EmployeesPayout|\Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payouts()
    {
        return $this->hasMany('App\EmployeesPayout', 'employee_id', 'id');
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return self::getHumanRole($this->role);
    }

    /**
     * @return Builder|GoodsPosition[]|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function positions()
    {
        return $this->hasMany('App\GoodsPosition', 'employee_id', 'id');
    }

    /**
     * @return Builder|Order[]
     */
    public function orders()
    {
        return Order::whereIn('position_id', $this->positions->pluck('id')->unique());
    }

    /**
     * @return string
     */
    public function getRating()
    {
        $rating = GoodsReview::whereIn('id', $this->orders()->pluck('review_id')->unique())
            ->select(DB::raw('count(*) as c, sum(dropman_rating) as dr'))
            ->first()
            ->toArray();

        if ($rating['c'] == 0) {
            return '0.00';
        }

        return sprintf('%.2f', $rating['dr'] / $rating['c']);
    }

    /**
     * Return employee balance.
     *
     * @param string $currency
     *
     * @return float
     */
    public function getBalance($currency = BitcoinUtils::CURRENCY_BTC)
    {
        return BitcoinUtils::convert($this->balance, BitcoinUtils::CURRENCY_RUB, $currency);
    }

    /**
     * @param string $currency
     *
     * @return string
     */
    public function getHumanBalance($currency = BitcoinUtils::CURRENCY_BTC)
    {
        return human_price($this->getBalance($currency), $currency);
    }

    /**
     * calculates the emoployee stats for period of time.
     */
    public function getStats($periodStart, $periodEnd): \Illuminate\Support\Collection
    {
        $positions = $this->positions()
//            ->whereBetween('goods_positions.created_at', [$periodStart, $periodEnd])
//            ->orWhereBetween('orders.created_at', [$periodStart, $periodEnd])
            ->leftJoin('orders', 'orders.position_id', '=', 'goods_positions.id')
            ->leftJoin('employees_earnings', 'employees_earnings.order_id', '=', 'orders.id')
            ->leftJoin('goods', 'goods.id', '=', 'goods_positions.good_id')
            ->leftJoin('goods_packages', 'goods_packages.id', '=', 'goods_positions.package_id')
            ->select([
                'goods_positions.id', 'goods_positions.available', 'goods_positions.moderated', 'goods_positions.created_at as pos_created_at',
                'orders.id as order_id',  'orders.package_price', 'orders.updated_at as order_updated_at',
                'orders.package_currency', 'orders.package_price_btc', 'orders.status', 'orders.status_was_problem',
                'employees_earnings.amount as earned',
                'goods.id as good_id', 'goods.title as goods_title',
                'goods_packages.amount as package_amount', 'goods_packages.measure as package_measure', 'goods_packages.id as package_id',
                'goods_packages.employee_reward as reward',
            ])->get();

        $goodsPackagesId = $positions->pluck('good_id', 'package_id')->filter()->toArray();
        $goods = Good::whereIn('id', array_unique(array_values($goodsPackagesId)))->get()->keyBy('id');
        $packages = GoodsPackage::whereIn('id', array_keys($goodsPackagesId))->get()->keyBy('id');

        if ($positions->count() < 1) {
            return collect([]);
        }

        $stats = [
            'positions_not_moderated_count' => 0, // всего позиций в модерации за период
            'positions_added_count' => 0, // всего позиций на витрине за период
            'positions_sell_count' => 0, // всего заказов за период
            'shop_earn' => [ // общая сумма package_price проданных товаров за период
                BitcoinUtils::CURRENCY_BTC => 0,
                BitcoinUtils::CURRENCY_USD => 0,
                BitcoinUtils::CURRENCY_RUB => 0,
            ],
            'employee_earn_orders' => [ // общая сумма "выплата курьеру" проданных квестов за период
                BitcoinUtils::CURRENCY_RUB => 0,
            ],
            'employee_earn_positions' => [ // общая сумма "выплата курьеру" загруженных квестов за период
                BitcoinUtils::CURRENCY_RUB => 0,
            ],
            'employee_penalties_sum' => [ // общая сумма штрафов
                BitcoinUtils::CURRENCY_RUB => 0,
            ],
            'employee_penalties_count' => 0, // кол-во штрафов
            'employee_disputes_count' => 0, // кол-во диспутов
            'goods_stats' => [], // статистика по товарам
        ];

        foreach ($goodsPackagesId as $package_id => $good_id) {
            $stats['goods_stats'][$package_id] = [
                'id' => $good_id,
                'package_id' => $package_id,
                'title' => $goods->has($good_id) ? $goods->get($good_id)->title : '-',
                'amount' => $packages->has($package_id) ? $packages->get($package_id)->getHumanWeight() : '-',
                'positions_added_count' => 0,
                'positions_sell_count' => 0,
                'employee_earn_orders' => [
                    BitcoinUtils::CURRENCY_RUB => 0,
                ],
                'employee_earn_positions' => [
                    BitcoinUtils::CURRENCY_RUB => 0,
                ],
                'employee_penalties_sum' => [
                    BitcoinUtils::CURRENCY_RUB => 0,
                ],
                'shop_earn' => [
                    BitcoinUtils::CURRENCY_BTC => 0,
                    BitcoinUtils::CURRENCY_USD => 0,
                    BitcoinUtils::CURRENCY_RUB => 0,
                ],
                'employee_disputes_count' => 0,
                'employee_penalties_count' => 0,
            ];
        }

        $positions->each(function ($pos) use (&$stats, $periodStart, $periodEnd): void {
            if ($pos->pos_created_at >= $periodStart && $pos->pos_created_at <= $periodEnd) {
                ++$stats['positions_added_count'];
                ++$stats['goods_stats'][$pos->package_id]['positions_added_count'];
                $stats['employee_earn_positions'][BitcoinUtils::CURRENCY_RUB] += $pos->reward;
                $stats['goods_stats'][$pos->package_id]['employee_earn_positions'][BitcoinUtils::CURRENCY_RUB] += $pos->reward;
            }

            if (!$pos->moderated) {
                ++$stats['positions_not_moderated_count'];
            }

            if (null !== $pos->order_id && $pos->order_updated_at >= $periodStart && $pos->order_updated_at <= $periodEnd) {
                ++$stats['positions_sell_count'];

                if ($pos->status === Order::STATUS_FINISHED) {
                    ++$stats['goods_stats'][$pos->package_id]['positions_sell_count'];
                    $stats['goods_stats'][$pos->package_id]['shop_earn'][$pos->package_currency] += $pos->package_price;
                    $stats['shop_earn'][$pos->package_currency] += $pos->package_price;

                    if (!array_key_exists(BitcoinUtils::CURRENCY_BTC, $stats['goods_stats'][$pos->package_id]['shop_earn'])) {
                        $stats['goods_stats'][$pos->package_id]['shop_earn'][BitcoinUtils::CURRENCY_BTC] += $pos->package_price_btc;
                    }

                    if (!array_key_exists(BitcoinUtils::CURRENCY_BTC, $stats['shop_earn'])) {
                        $stats['shop_earn'][BitcoinUtils::CURRENCY_BTC] += $pos->package_price_btc;
                    }

                    if ($pos->earned < 0) {
                        ++$stats['employee_penalties_count'];
                        ++$stats['goods_stats'][$pos->package_id]['employee_penalties_count'];
                        $stats['employee_penalties_sum'][BitcoinUtils::CURRENCY_RUB] += $pos->earned;
                        $stats['goods_stats'][$pos->package_id]['employee_penalties_sum'][BitcoinUtils::CURRENCY_RUB] += $pos->earned;
                    } else {
                        $stats['employee_earn_orders'][BitcoinUtils::CURRENCY_RUB] += $pos->earned;
                        $stats['goods_stats'][$pos->package_id]['employee_earn_orders'][BitcoinUtils::CURRENCY_RUB] += $pos->earned;
                    }

                    if ($pos->status_was_problem) {
                        ++$stats['employee_disputes_count'];
                        ++$stats['goods_stats'][$pos->package_id]['employee_disputes_count'];
                    }
                } elseif ($pos->status === Order::STATUS_PROBLEM) {
                    ++$stats['employee_disputes_count'];
                    ++$stats['goods_stats'][$pos->package_id]['employee_disputes_count'];
                }
            }
        });

        return collect($stats);
    }

    public function getPublicName()
    {
        return $this->user->role !== User::ROLE_SHOP ? e(config('mm2.application_title') . ' (' . $this->user->username . ')') : config('mm2.application_title');
    }

    public function getPublicDecoratedName()
    {
        return '<b class="text-info">' . e($this->getPublicName()) . '</b>';
    }

    public function getPrivateName()
    {
        return $this->user->role !== User::ROLE_SHOP ? $this->user->username : config('mm2.application_title');
    }
}
