<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsSecService extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table): void {
            $table->boolean('withdraw_shop_wallet')->default(true)->after('orders_chart_url');
            $table->string('disabled_reason')->default(null)->nullable()->after('withdraw_shop_wallet');
        });

        Schema::create('shop_overrides', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('shop_id')->unsigned()->index();
            $table->string('param', 64)->index();
            $table->string('value', 128);
            $table->unique(['shop_id', 'param']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table): void {
            $table->dropColumn('withdraw_shop_wallet');
            $table->dropColumn('disabled_reason');
        });

        Schema::dropIfExists('shop_overrides');
    }
}
