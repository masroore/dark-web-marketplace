<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangesReservedTime extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('qiwi_exchanges', function (Blueprint $table): void {
            $table->integer('reserve_time')->after('btc_rub_rate')->default(15);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qiwi_exchanges', function (Blueprint $table): void {

        });
    }
}
