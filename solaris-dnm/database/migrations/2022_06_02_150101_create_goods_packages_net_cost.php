<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsPackagesNetCost extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('goods_packages', function (Blueprint $table): void {
            $table->integer('net_cost')->default(null)->nullable(true)->after('currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_packages', function (Blueprint $table): void {
            $table->dropColumn('net_cost');
        });
    }
}
