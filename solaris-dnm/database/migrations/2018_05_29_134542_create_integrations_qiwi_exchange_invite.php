<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntegrationsQiwiExchangeInvite extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table): void {
            //    $table->string('integrations_qiwi_exchange_invite')->nullable()->default(null)->after('integrations_qiwi_api_last_sync_at');
            //    $table->integer('integrations_qiwi_exchange_id')->nullable()->default(null)->after('integrations_qiwi_exchange_invite');
        });

        $shop = App\Shop::getDefaultShop();
        // $shop->integrations_qiwi_exchange_invite = \Illuminate\Support\Str::random();
        // $shop->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table): void {

        });
    }
}
