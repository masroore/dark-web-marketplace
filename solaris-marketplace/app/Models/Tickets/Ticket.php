<?php

namespace App\Models\Tickets;

use App\Model;
use App\User;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * App\Models\Tickets\Ticket.
 *
 * @property int $id
 * @property string $title
 * @property string $category
 * @property int $user_id
 * @property bool $closed
 * @property \Carbon\Carbon $created_at
 * @property null|\Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $last_message_at
 * @property User $author
 * @property \App\Models\Tickets\Message[]|\Illuminate\Database\Eloquent\Collection $messages
 *
 * @method static Builder|Ticket applySearchFilters(Request $request)
 * @method static Builder|Ticket filterCategory($category)
 * @method static Builder|Ticket filterStatus($status)
 * @method static Builder|Ticket filterTitle($title)
 * @method static Builder|Ticket filterUser($username)
 * @method static Builder|Ticket owned($user_id)
 * @method static Builder|Ticket whereCategory($value)
 * @method static Builder|Ticket whereClosed($value)
 * @method static Builder|Ticket whereCreatedAt($value)
 * @method static Builder|Ticket whereId($value)
 * @method static Builder|Ticket whereLastMessageAt($value)
 * @method static Builder|Ticket whereTitle($value)
 * @method static Builder|Ticket whereUpdatedAt($value)
 * @method static Builder|Ticket whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Ticket extends Model
{
    public const CATEGORY_COMMON_SELLER_QUESTION = 'common_seller_question';
    public const CATEGORY_COMMON_BUYER_QUESTION = 'common_buyer_question';
    public const CATEGORY_APPLICATION_FOR_OPENING = 'app_for_opening';
    public const CATEGORY_COOPERATION = 'cooperation';
    public const CATEGORY_SECURITY_SERVICE = 'security_service';
    public const CATEGORY_PAYMENT_ERRORS = 'payment_errors';

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    protected $table = 'tickets';

    protected $fillable = [
        'title', 'category', 'user_id', 'closed', 'last_message_at',
    ];

    public static $defaultCounters = [
        self::CATEGORY_COMMON_SELLER_QUESTION => 0, self::CATEGORY_COMMON_BUYER_QUESTION => 0,
        self::CATEGORY_APPLICATION_FOR_OPENING => 0, self::CATEGORY_COOPERATION => 0,
        self::CATEGORY_SECURITY_SERVICE => 0, self::CATEGORY_PAYMENT_ERRORS => 0,
    ];

    /**
     * @return array[]
     */
    public static function getCounters(): array
    {
        $openedCounters = DB::query()
            ->selectRaw('category, count(id) as cnt')
            ->from('tickets')
            ->where('closed', 0)
            ->groupBy(['category'])
            ->pluck('cnt', 'category')
            ->toArray();

        $closedCounters = DB::query()
            ->selectRaw('category, count(id) as cnt')
            ->from('tickets')
            ->where('closed', 1)
            ->groupBy(['category'])
            ->pluck('cnt', 'category')
            ->toArray();

        return [
            'opened' => array_merge(self::$defaultCounters, $openedCounters),
            'closed' => array_merge(self::$defaultCounters, $closedCounters),
        ];
    }

    public function messages(): HasMany
    {
        return $this->hasMany('App\Models\Tickets\Message', 'ticket_id', 'id');
    }

    public function author(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function scopeApplySearchFilters(Builder $tickets, Request $request): Builder
    {
        if (!empty($category = $request->get('category'))) {
            $tickets = $tickets->filterCategory($category);
        }

        if (!empty($status = $request->get('status'))) {
            $tickets = $tickets->filterStatus($status);
        }

        if (!empty($title = $request->get('title'))) {
            $tickets = $tickets->filterTitle($title);
        }

        // allow filter by user id
        if (Auth::user()->isAdmin() && !empty($username = $request->get('username'))) {
            $tickets = $tickets->filterUser($username);
        }

        return $tickets;
    }

    public function scopeOwned($query, $user_id): Builder
    {
        return $query->where('user_id', '=', $user_id);
    }

    public function scopeFilterCategory($query, $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeFilterStatus($query, $status): Builder
    {
        switch ($status) {
            case 'opened':
                $query = $query->where('closed', '=', false);

                break;

            case 'closed':
                $query = $query->where('closed', '=', true);

                break;
        }

        return $query;
    }

    public function scopeFilterUser($query, $username): Builder
    {
        $userIds = User::where('username', 'LIKE', '%' . trim($username) . '%')->pluck('id');

        return $query->whereIn('user_id', $userIds);
    }

    public function scopeFilterTitle($query, $title): Builder
    {
        return $query->where('title', 'LIKE', '%' . trim($title) . '%');
    }
}
