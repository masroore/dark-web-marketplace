<?php

namespace App\Marketplace\Payment;

use App\Marketplace\Utility\FeeCalculator;
use App\Purchase;
use Exception;

class Escrow extends Payment
{
    /**
     * Procedure when the purchase is created.
     */
    public function purchased(): void
    {
        // generate escrow address as the account pass the Purchase id
        $this->purchase->address = $this->coin->generateAddress(['user' => $this->purchase->id]);
    }

    /**
     * Empty procedure for sent.
     */
    public function sent(): void
    {
    }

    /**
     * Release funds to the vendor.
     */
    public function delivered(): void
    {
        // fee that needs to be caluclated
        $feeCaluclator = new FeeCalculator($this->purchase->to_pay);

        // make array of receivers
        $receiversAmounts = [
            // vendor receiver
            $this->purchase->vendor->user->coinAddress($this->coinLabel())->address => $feeCaluclator->getBase(),
        ];

        // check if user has refered user
        $hasReferral = $this->purchase->buyer->hasReferredBy();

        // set the buyer's referred by user into receivers
        if ($hasReferral) {
            $referredByUserAddress = $this->purchase->buyer->referredBy->coinAddress($this->coinLabel())->address;

            $receiversAmounts[$referredByUserAddress] = $feeCaluclator->getFee($hasReferral);
        }

        // send the funds to the random address of the market
        $marketplaceAddresses = config('coins.market_addresses.' . $this->coinLabel());
        if (!empty($marketplaceAddresses)) {
            $randomMarketAddress = $marketplaceAddresses[array_rand($marketplaceAddresses)];
            $receiversAmounts[$randomMarketAddress] = $feeCaluclator->getFee($hasReferral);
        }

        // call a coin procedure to send funds
        $this->coin->sendToMany($receiversAmounts);

    }

    /**
     * Resolve by sending funds to passed address.
     */
    public function resolved(array $parameters): void
    {
        if (!array_key_exists('receiving_address', $parameters)) {
            throw new Exception('There is no receiving address defined!');
        }

        // calculate fee
        $feeCaluclator = new FeeCalculator($this->purchase->to_pay);

        // make array of receivers
        $receiversAmounts = [
            $parameters['receiving_address'] => $feeCaluclator->getBase(),
        ];

        // send the funds to the random address
        $marketplaceAddresses = config('coins.market_addresses.' . $this->coinLabel());
        if (!empty($marketplaceAddresses)) {
            // set the market address as a receiver
            $randomMarketAddress = $marketplaceAddresses[array_rand($marketplaceAddresses)];

            $receiversAmounts[$randomMarketAddress] = $feeCaluclator->getFee();
        }

        // call a coin procedure to send funds
        $this->coin->sendToMany($receiversAmounts);

    }

    /**
     * Returns balance of the purchase's address.
     */
    public function balance(): float
    {
        return $this->coin->getBalance(['account' => $this->purchase->id, 'address' => $this->purchase->address]);
    }

    /**
     * Convert to amount of coin.
     */
    public function usdToCoin($usd): float
    {
        return $this->coin->usdToCoin($usd);
    }

    /**
     * Return Coin's label.
     */
    public function coinLabel(): string
    {
        return $this->coin->coinLabel();
    }

    /**
     * Procedure when the purchase is canceled.
     */
    public function canceled(): void
    {
        // if there is balance on the address
        if (($balanceAddres = $this->balance()) > 0) {
            // fee that needs to be caluclated
            $feeCaluclator = new FeeCalculator($balanceAddres);

            // make array of receivers
            $receiversAmounts = [
                // buyer receiver
                $this->purchase->buyer->coinAddress($this->coinLabel())->address => $feeCaluclator->getBase(),
            ];

            // check if user has refered user
            $hasReferral = false; // no referal on canceled purchases

            // send the funds to the random address of the market
            $marketplaceAddresses = config('coins.market_addresses.' . $this->coinLabel());
            if (!empty($marketplaceAddresses)) {
                $randomMarketAddress = $marketplaceAddresses[array_rand($marketplaceAddresses)];
                $receiversAmounts[$randomMarketAddress] = $feeCaluclator->getFee($hasReferral);
            }

            // call a coin procedure to send funds to a buyer and to market
            $this->coin->sendToMany($receiversAmounts);

        }

    }
}
