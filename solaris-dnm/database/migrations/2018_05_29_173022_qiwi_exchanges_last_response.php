<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class QiwiExchangesLastResponse extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('qiwi_exchanges', function (Blueprint $table): void {
            $table->mediumText('last_response')->nullable()->default(null)->after('active');
            $table->timestamp('last_response_at')->nullable()->default(null)->after('last_response');
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
