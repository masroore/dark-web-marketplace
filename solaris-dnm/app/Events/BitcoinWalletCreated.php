<?php

namespace App\Events;

use App\Shop;
use App\User;
use App\Wallet;
use Event;
use Illuminate\Queue\SerializesModels;

class BitcoinWalletCreated extends Event
{
    use SerializesModels;

    /** @var Wallet */
    public $wallet;

    /** @var User */
    public $user;

    /** @var Shop */
    public $shop;

    /**
     * Create a new event instance.
     */
    public function __construct(Wallet $wallet)
    {
        $this->wallet = $wallet;
        if ($this->wallet->user) {
            $this->user = $this->wallet->user;
        } elseif ($this->wallet->shop) {
            $this->shop = $this->wallet->shop;
        }
    }
}
