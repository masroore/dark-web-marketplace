<?php

namespace App\Jobs;

use App\Packages\ExchangeAPI\ExchangeAPI;
use App\QiwiExchangeRequest;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateExchange implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var QiwiExchangeRequest */
    protected $exchangeRequest;

    /**
     * Create a new job instance.
     */
    public function __construct(QiwiExchangeRequest $exchangeRequest)
    {
        $this->exchangeRequest = $exchangeRequest;
    }

    /**
     * Execute the job.
     */
    public function handle(ExchangeAPI $exchangeAPI): void
    {
        $exchangeAPI->setQiwiExchange($this->exchangeRequest->qiwiExchange);

        $exchangeTransaction = false;

        try {
            $exchangeTransaction = $exchangeAPI->makeExchange($this->exchangeRequest);
            $this->exchangeRequest->status = QiwiExchangeRequest::STATUS_RESERVED;
            $this->exchangeRequest->save();
        } catch (RequestException $exception) {
            if ($this->attempts() !== 2) { // try 3 times, then mark as cancelled
                sleep(5);

                throw $exception;
            }
        }

        if (!$exchangeTransaction) {
            $this->exchangeRequest->status = QiwiExchangeRequest::STATUS_CANCELLED;
            $this->exchangeRequest->save();
        }
    }

    public function failed(): void
    {
        $this->exchangeRequest->error_reason = 'Ошибка соединения с сервером обменника.';
        $this->exchangeRequest->status = QiwiExchangeRequest::STATUS_CANCELLED;
        $this->exchangeRequest->save();
    }
}
