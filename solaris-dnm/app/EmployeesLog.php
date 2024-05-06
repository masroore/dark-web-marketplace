<?php

namespace App;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * App\EmployeesLog.
 *
 * @property int $id
 * @property int $shop_id
 * @property int $employee_id
 * @property int $item_id
 * @property string $action
 * @property string $data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property Good $good
 * @property GoodsPackage $package
 * @property GoodsPosition $position
 *
 * @method static \Illuminate\Database\Query\Builder|\App\EmployeesLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmployeesLog whereShopId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmployeesLog whereEmployeeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmployeesLog whereItemId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmployeesLog whereAction($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmployeesLog whereData($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmployeesLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmployeesLog whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 *
 * @property int $good_id
 * @property int $package_id
 * @property int $position_id
 * @property int $order_id
 * @property int $page_id
 * @property Order $order
 * @property Employee $employee
 *
 * @method static \Illuminate\Database\Query\Builder|\App\EmployeesLog whereGoodId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmployeesLog wherePackageId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmployeesLog wherePositionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmployeesLog whereOrderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\EmployeesLog wherePageId($value)
 *
 * @property Page $page
 *
 * @method static \Illuminate\Database\Query\Builder|\App\EmployeesLog applySearchFilters($request)
 * @method static \Illuminate\Database\Query\Builder|\App\EmployeesLog filterEmployee($employeeId)
 */
class EmployeesLog extends Model
{
    public const ACTION_GOODS_ADD = 'goods_add';
    public const ACTION_GOODS_EDIT = 'goods_edit';
    public const ACTION_GOODS_DELETE = 'goods_delete';
    public const ACTION_PACKAGES_ADD = 'packages_add';
    public const ACTION_PACKAGES_EDIT = 'packages_edit';
    public const ACTION_PACKAGES_DELETE = 'packages_delete';
    public const ACTION_QUESTS_ADD = 'quests_add';
    public const ACTION_QUESTS_EDIT = 'quests_edit';
    public const ACTION_QUESTS_DELETE = 'quests_delete';
    public const ACTION_ORDERS_PREORDER = 'orders_preorder';
    public const ACTION_FINANCE_PAYOUT = 'finance_payout';
    public const ACTION_SETTINGS_PAGE_ADD = 'settings_page_add';
    public const ACTION_SETTINGS_PAGE_EDIT = 'settings_page_edit';
    public const ACTION_SETTINGS_PAGE_DELETE = 'settings_page_delete';
    public const ACTION_QUESTS_MODERATE_ACCEPT = 'quests_moderate_accept';
    public const ACTION_QUESTS_MODERATE_DECLINE = 'quests_moderate_decline';

    protected $table = 'employees_logs';

    protected $primaryKey = 'id';

    protected $fillable = [
        'shop_id', 'employee_id', 'item_id', 'good_id', 'package_id', 'position_id',
        'order_id', 'page_id', 'action', 'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * @return Builder|Good|\Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function good()
    {
        return $this->belongsTo('App\Good', 'good_id', 'id');
    }

    /**
     * @return Builder|GoodsPackage|\Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function package()
    {
        return $this->belongsTo('App\GoodsPackage', 'package_id', 'id');
    }

    /**
     * @return Builder|GoodsPosition|\Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function position()
    {
        return $this->belongsTo('App\GoodsPosition', 'position_id', 'id');
    }

    /**
     * @return Builder|\Illuminate\Database\Eloquent\Relations\BelongsTo|Order
     */
    public function order()
    {
        return $this->belongsTo('App\Order', 'order_id', 'id');
    }

    /**
     * @return Builder|Employee|\Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo('App\Employee', 'employee_id', 'id');
    }

    /**
     * @return Builder|\Illuminate\Database\Eloquent\Relations\BelongsTo|Page
     */
    public function page()
    {
        return $this->belongsTo('App\Page', 'page_id', 'id');
    }

    public function getHumanAction()
    {
        switch ($this->action) {
            case self::ACTION_GOODS_ADD:
                return 'Добавлен товар';

            case self::ACTION_GOODS_EDIT:
                return 'Отредактирован товар';

            case self::ACTION_GOODS_DELETE:
                return 'Удален товар';

            case self::ACTION_PACKAGES_ADD:
                return 'Добавлена упаковка';

            case self::ACTION_PACKAGES_EDIT:
                return 'Отредактирована упаковка';

            case self::ACTION_PACKAGES_DELETE:
                return 'Удалена упаковка';

            case self::ACTION_QUESTS_ADD:
                return 'Добавлен квест';

            case self::ACTION_QUESTS_EDIT:
                return 'Отредактирован квест';

            case self::ACTION_QUESTS_DELETE:
                return 'Удален квест';

            case self::ACTION_ORDERS_PREORDER:
                return 'Выдан предзаказ';

            case self::ACTION_FINANCE_PAYOUT:
                return 'Совершена выплата';

            case self::ACTION_SETTINGS_PAGE_ADD:
                return 'Добавлена страница';

            case self::ACTION_SETTINGS_PAGE_EDIT:
                return 'Отредактирована страница';

            case self::ACTION_SETTINGS_PAGE_DELETE:
                return 'Удалена страница';

            case self::ACTION_QUESTS_MODERATE_ACCEPT:
                return 'Принят квест из модерации';

            case self::ACTION_QUESTS_MODERATE_DECLINE:
                return 'Удален квест из модерации';

            default:
                return 'Unknown action: ' . $this->action;
        }
    }

    public function scopeApplySearchFilters(Builder $employeesLog, Request $request)
    {
        if (!empty($employeeId = $request->get('employee'))) {
            $employeesLog = $employeesLog->filterEmployee($employeeId);
        }

        return $employeesLog;
    }

    public function scopeFilterEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Log user activity.
     *
     * @param null|array $belongings
     */
    public static function log(User $user, $action, array $belongings = [], ?array $data = null): void
    {
        if (!$user->employee) {
            throw new Exception('User does not belong to any shop.');
        }

        self::create(array_merge($belongings, [
            'shop_id' => $user->employee->shop_id,
            'employee_id' => $user->employee->id,
            'action' => $action,
            'data' => $data,
        ]));
    }
}
