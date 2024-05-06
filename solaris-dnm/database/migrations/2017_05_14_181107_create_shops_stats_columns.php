<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsStatsColumns extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table): void {
            $table->string('visitors_chart_url')->nullable()->default(null)->after('qiwi_count');
            $table->string('orders_chart_url')->nullable()->default(null)->after('visitors_chart_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table): void {
            $table->dropColumn(['visitors_chart_url', 'orders_chart_url']);
        });
    }
}
