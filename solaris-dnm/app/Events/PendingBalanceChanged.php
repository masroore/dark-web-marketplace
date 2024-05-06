<?php

namespace App\Events;

use App\Shop;
use App\Transaction;
use App\User;
use App\Wallet;
use Event;
use Illuminate\Queue\SerializesModels;

class PendingBalanceChanged extends Event
{
    use SerializesModels;

    /** @var Wallet */
    public $wallet;

    /** @var User */
    public $user;

    /** @var Shop */
    public $shop;

    /**
     * @var Transaction
     */
    public $transaction;

    /**
     * Create a new event instance.
     */
    public function __construct(Wallet $wallet, Transaction $transaction)
    {
        $this->wallet = $wallet;
        if ($this->wallet->user) {
            $this->user = $this->wallet->user;
        } elseif ($this->wallet->shop) {
            $this->shop = $this->wallet->shop;
        }
        $this->transaction = $transaction;
    }
}
