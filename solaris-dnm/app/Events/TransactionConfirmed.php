<?php

namespace App\Events;

use App\Transaction;
use Event;
use Illuminate\Queue\SerializesModels;

class TransactionConfirmed extends Event
{
    use SerializesModels;

    /** @var Transaction */
    public $transaction;

    /**
     * Create a new event instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }
}
