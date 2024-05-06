<?php

namespace App\Marketplace\Payment;

interface Coin
{
    /**
     * Pass the 'user' string to generate address as 'user's address
     * Returns the newly generated address.
     */
    public function generateAddress(array $parameters = []): string;

    /**
     * Returns the balance of the addresses under the account defined  by 'account' key in $parameters.
     */
    public function getBalance(array $parameters = []): float;

    /**
     * Send amount from account to address.
     *
     * @return mixed
     */
    public function sendToAddress(string $toAddress, float $amount);

    /**
     * Send amount from account to addresses ith amounts.
     *
     * @param string $addressesAmounts [[ 'address' => 1.01 ], [ 'address2' => 2.02 ]]
     *
     * @return mixed
     */
    public function sendToMany(array $addressesAmounts);

    /**
     * Converts from the amount USD to the amount of the coin.
     *
     * @param float $usd amount of the usd
     *
     * @return float amount of the coin
     */
    public function usdToCoin($usd): float;

    /**
     * Three characters long text representation of the coin.
     */
    public function coinLabel(): string;
}
