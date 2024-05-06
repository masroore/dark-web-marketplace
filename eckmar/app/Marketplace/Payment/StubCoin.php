<?php

namespace App\Marketplace\Payment;

use Illuminate\Support\Facades\Log;

/**
 * Class that simulates interface of the coin.
 */
class StubCoin implements Coin
{
    public function generateAddress(array $parameters = []): string
    {
        return 'addressStub#' . mt_rand(0, 999999);
    }

    public function getBalance(array $parameters = []): float
    {
        return 101;
    }

    public function sendFrom(string $fromAccount, string $toAddress, float $amount): void
    {
        Log::info('From accout ' . $fromAccount . ' to address ' . $toAddress . ' to amount ' . $amount);
    }

    public function sendToMany(array $addressesAmounts): void
    {

        foreach ($addressesAmounts as $adr) {
            Log::info("STB Transaction to address $adr");
        }
    }

    public function usdToCoin($usd): float
    {
        return $usd;
    }

    public function coinLabel(): string
    {
        return 'stb';
    }

    public function sendToAddress(string $toAddress, float $amount): void
    {
        Log::info('Sending to address ' . $toAddress . ' to amount ' . $amount);
    }
}
