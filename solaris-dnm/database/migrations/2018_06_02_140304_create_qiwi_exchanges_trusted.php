<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQiwiExchangesTrusted extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('qiwi_exchanges', function (Blueprint $table): void {
            $table->boolean('trusted')->default(false)->after('active');
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
