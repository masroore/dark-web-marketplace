<?php

declare(strict_types=1);

namespace App;

use App\Packages\FetchingImagesTrait;
use App\Packages\Stub;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;

/**
 * Заказ.
 *
 * @property int         $id
 * @property int         $user_id
 * @property int         $city_id
 * @property null|int    $position_id
 * @property null|int    $review_id
 * @property null|int    $good_id
 * @property string      $app_id
 * @property int         $app_order_id
 * @property int         $app_good_id
 * @property string      $good_title
 * @property string      $good_image_url
 * @property null|string $good_image_url_local
 * @property int         $good_image_cached
 * @property float       $package_amount
 * @property string      $package_measure
 * @property float       $package_price
 * @property string      $package_currency
 * @property int         $package_preorder
 * @property null|string $package_preorder_time
 * @property string      $status
 * @property null|string $comment
 * @property null|string $app_created_at
 * @property null|string $app_updated_at
 * @property null|Carbon $created_at            Дата создания.
 * @property null|Carbon $updated_at            Дата последнего обновления.
 * @property null|Carbon $last_sync_at          Дата последней синхронизации с магазином.
 * @property OrderPosition $position
 * @property null|Good $good
 * @property OrderReview $review
 * @property Shop $shop
 * @property User $user
 *
 * @method static Builder|Order whereAppCreatedAt($value)
 * @method static Builder|Order whereAppGoodId($value)
 * @method static Builder|Order whereAppId($value)
 * @method static Builder|Order whereAppOrderId($value)
 * @method static Builder|Order whereAppUpdatedAt($value)
 * @method static Builder|Order whereCityId($value)
 * @method static Builder|Order whereComment($value)
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereGoodId($value)
 * @method static Builder|Order whereGoodImageCached($value)
 * @method static Builder|Order whereGoodImageUrl($value)
 * @method static Builder|Order whereGoodImageUrlLocal($value)
 * @method static Builder|Order whereGoodTitle($value)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order wherePackageAmount($value)
 * @method static Builder|Order wherePackageCurrency($value)
 * @method static Builder|Order wherePackageMeasure($value)
 * @method static Builder|Order wherePackagePreorder($value)
 * @method static Builder|Order wherePackagePreorderTime($value)
 * @method static Builder|Order wherePackagePrice($value)
 * @method static Builder|Order wherePositionId($value)
 * @method static Builder|Order whereReviewId($value)
 * @method static Builder|Order whereStatus($value)
 * @method static Builder|Order whereUpdatedAt($value)
 * @method static Builder|Order whereUserId($value)
 * @method static Builder|Order applySearchFilters(Request $request)
 * @method static Builder|Order filterStatus($status)
 * @method static where(string $string, string $string1, int|mixed $id)
 * @method static with(string[] $array)
 */
class Order extends Model
{
    use FetchingImagesTrait;

    public const STATUS_PREORDER_PAID = 'preorder_paid';
    public const STATUS_PAID = 'paid';
    public const STATUS_PROBLEM = 'problem';
    public const STATUS_FINISHED_AFTER_DISPUTE = 'finished_after_dispute';
    public const STATUS_CANCELLED_AFTER_DISPUTE = 'cancelled_after_dispute';
    public const STATUS_FINISHED = 'finished';
    public const STATUS_CANCELLED = 'cancelled';

    /** {@inheritdoc} */
    protected $table = 'orders';

    /** {@inheritdoc} */
    protected $primaryKey = 'id';

    protected $remoteImageURLColumn = 'good_image_url';

    protected $localImageURLColumn = 'good_image_url_local';

    protected $localImageCachedColumn = 'good_image_cached';

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'app_id', 'app_id');
    }

    public function good(): BelongsTo
    {
        return $this->belongsTo(Good::class, 'good_id', 'id');
    }

    public function position(): HasOne
    {
        return $this->hasOne(OrderPosition::class, 'order_id', 'id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(OrderReview::class, 'order_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return Builder
     */
    public function scopeApplySearchFilters(Builder $orders, Request $request)
    {
        if (!empty($status = $request->get('status'))) {
            $orders = $orders->filterStatus($status);
        }

        return $orders;
    }

    /**
     * @return mixed
     */
    public function scopeFilterStatus($query, $status)
    {
        if ($status === 'active') {
            return $query->whereNotIn('status', [self::STATUS_FINISHED, self::STATUS_CANCELLED]);
        }

        return $query->where('status', $status);
    }

    /**
     * @return Good|Stub
     */
    public function _stub_good()
    {
        return stub('Good', [
            'id' => $this->good_id,
            'app_id' => $this->app_id,
            'city_id' => $this->city_id,
            'title' => $this->good_title,
            'image_url' => $this->good_image_url,
        ]);
    }

    /**
     * @return GoodsPackage|Stub
     */
    public function _stub_package()
    {
        return stub('GoodsPackage', [
            'amount' => $this->package_amount,
            'measure' => $this->package_measure,
            'price' => $this->package_price,
            'currency' => $this->package_currency,
            'preorder' => $this->package_preorder,
            'preorder_time' => $this->package_preorder_time,
        ]);
    }

    /**
     * Returns time before address should be hidden in human-readable format.
     */
    public function getHumanQuestRemainingTime(): string
    {
        $diff = $this->getQuestRemainingTime() / 60;
        $hours = floor($diff / 60);
        $minutes = $diff % 60;

        return sprintf(
            '%d %s %d %s',
            $hours,
            plural($hours, ['час', 'часа', 'часов']),
            $minutes,
            plural($minutes, ['минуту', 'минуты', 'минут'])
        );
    }

    /**
     * Returns time in seconds before address should be hidden.
     *
     * @return int
     */
    public function getQuestRemainingTime()
    {
        return config('catalog.order_quest_time') * 3600 - Carbon::now()->diffInSeconds($this->app_created_at, true);
    }

    public function getHumanPreorderRemainingTime(): string
    {
        $diff = $this->getPreorderRemainingTime() / 60;
        $hours = floor($diff / 60);
        $minutes = $diff % 60;

        return sprintf(
            '%d %s %d %s',
            $hours,
            plural($hours, ['час', 'часа', 'часов']),
            $minutes,
            plural($minutes, ['минуту', 'минуты', 'минут'])
        );
    }

    /**
     * @return int
     */
    public function getPreorderRemainingTime()
    {
        $preorderTime = config('catalog.preorder_close_time') * 3600;

        return $preorderTime - Carbon::now()->diffInSeconds($this->app_updated_at, true);
    }

    public function getHumanStatus(): string
    {
        switch ($this->status) {
            case self::STATUS_PAID:
                return 'Ожидает отзыва';

            case self::STATUS_PREORDER_PAID:
                return 'Ожидает доставки';

            case self::STATUS_FINISHED:
            case self::STATUS_FINISHED_AFTER_DISPUTE:
                return 'Завершен';

            case self::STATUS_PROBLEM:
                return 'Проблема';

            case self::STATUS_CANCELLED_AFTER_DISPUTE:
            case self::STATUS_CANCELLED:
                return 'Отменен';

            default:
                return 'Неизвестно';
        }
    }
}
