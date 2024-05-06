<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangesMinMax extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('qiwi_exchanges', function (Blueprint $table): void {
            $table->double('min_amount', 16, 8)->default(100)->after('reserve_time');
            $table->double('max_amount', 16, 8)->default(15000)->after('min_amount');
        });

        Schema::table('qiwi_exchanges_requests', function (Blueprint $table): void {
            $table->string('input')->nullable()->default(null)->after('btc_rub_rate');
        });

        Schema::table('qiwi_exchanges_transactions', function (Blueprint $table): void {
            $table->boolean('pay_need_input')->default(false)->after('pay_comment');
            $table->string('pay_input_description')->nullable()->default(null)->after('pay_need_input');
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
