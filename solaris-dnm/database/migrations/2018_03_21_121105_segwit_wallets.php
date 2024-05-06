<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SegwitWallets extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table): void {
            $table->string('segwit_wallet')->nullable()->default(null)->after('wallet_key');
        });

        /** @var App\Packages\Utils\BitcoinUtils $utils */
        $utils = resolve('App\Packages\Utils\BitcoinUtils');

        $accountName = config('mm2.application_id');
        foreach (App\Wallet::all() as $wallet) {
            $segwit = $utils->sendCommand(new Nbobtc\Command\Command('addwitnessaddress', $wallet->wallet));
            $utils->sendCommand(new Nbobtc\Command\Command('setaccount', [$segwit->result, $accountName]));
            $wallet->segwit_wallet = $segwit->result;
            $wallet->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table): void {

        });
    }
}
