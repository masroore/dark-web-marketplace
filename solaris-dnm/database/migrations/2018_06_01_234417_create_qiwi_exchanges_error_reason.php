<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQiwiExchangesErrorReason extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('qiwi_exchanges_requests', function (Blueprint $table): void {
            $table->string('error_reason')->nullable()->default(null)->after('status');
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
