<?php

namespace App\Listeners;

use App\Events\TransactionConfirmed;
use App\Events\TransactionCreated;
use App\Packages\Loggers\BitcoinLogger;
use App\Packages\Utils\BitcoinUtils;
use App\Transaction;

class TransactionEventListener
{
    /** @var BitcoinLogger */
    public $log;

    /**
     * Create the event listener.
     */
    public function __construct(BitcoinLogger $log)
    {
        $this->log = $log;
    }

    /**
     * Performs when transaction is just created.
     */
    public function created(TransactionCreated $event): void
    {
    }

    /**
     * Performs when transaction was confirmed.
     */
    public function confirmed(TransactionConfirmed $event): void
    {
        $transaction = $event->transaction;
        $this->log->info('Transaction confirmed.', [
            'tx_id' => $transaction->tx_id,
            'vout' => $transaction->vout,
        ]);
        $transaction->handled = true;
        $transaction->save();

        $event->transaction->wallet->balanceOperation(
            $transaction->amount,
            BitcoinUtils::CURRENCY_BTC,
            'Пополнение баланса'
        );
    }
}
