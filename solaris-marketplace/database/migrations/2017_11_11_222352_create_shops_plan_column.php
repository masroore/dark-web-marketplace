<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsPlanColumn extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table): void {
            $table->enum('plan', [
                App\Shop::PLAN_BASIC,
                App\Shop::PLAN_ADVANCED,
                App\Shop::PLAN_INDIVIDUAL,
                App\Shop::PLAN_FEE,
                App\Shop::PLAN_INDIVIDUAL_FEE,
            ])->nullable()->default(null)->after('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table): void {
            $table->dropColumn('plan');
        });
    }
}
