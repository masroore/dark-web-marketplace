<?php

namespace App\Listeners;

use App\Events\ActualBalanceChanged;
use App\Events\BitcoinWalletCreated;
use App\Events\PendingBalanceChanged;
use App\Jobs\CreateBitcoinWallet;
use App\Packages\Loggers\ShopLogger;
use App\Packages\Utils\BitcoinUtils;
use App\Shop;
use App\User;
use App\Wallet;
use Illuminate\Auth\Events\Registered;
use Log;

class UserEventListener
{
    /** @var ShopLogger */
    public $shopLogger;

    /**
     * Create the event listener.
     */
    public function __construct(ShopLogger $shopLogger)
    {
        $this->shopLogger = $shopLogger;
    }

    /**
     * Occurs when new user registered at the system.
     */
    public function registered(Registered $event): void
    {
        $job = new CreateBitcoinWallet($event->user, Wallet::TYPE_PRIMARY, ['title' => 'Основной кошелек пользователя']);
        dispatch($job);
    }

    /**
     * Occurs when Bitcoin wallet for user is ready.
     */
    public function walletCreated(BitcoinWalletCreated $event): void
    {
        if (!$event->user) {
            return;
        }

        $user = $event->user;
        Log::info('Registration is finished, marking user ' . $user->username . ' as active.');
        $user->active = true;
        $user->save();
    }

    /**
     * Occurs when pending balance has changed.
     */
    public function pendingBalanceChanged(PendingBalanceChanged $event): void
    {
    }

    /**
     * Occurs when actual balance has changed.
     */
    public function actualBalanceChanged(ActualBalanceChanged $event): void
    {
        if (!$event->user) {
            return;
        }

        $user = $event->user;

        // Shop activation.
        if ($user->role === User::ROLE_SHOP_PENDING) {
            $balance = $user->getRealBalance(BitcoinUtils::CURRENCY_USD);
            $shopPrice = config('mm2.shop_usd_price');

            if ($balance >= $shopPrice * config('mm2.shop_usd_price_approx')) {
                $user->balanceOperation(-$shopPrice, BitcoinUtils::CURRENCY_USD, 'Оплата моментального магазина');
                Shop::init($user);
                $this->shopLogger->alert('New shop created.', ['user_id' => $user->id, 'shop_id' => $user->shop()->id]);
            }
        }
    }
}
