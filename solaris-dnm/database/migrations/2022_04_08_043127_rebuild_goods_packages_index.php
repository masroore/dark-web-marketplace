<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RebuildGoodsPackagesIndex extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('goods_packages', function (Blueprint $table): void {
            $table->dropIndex('shop_id');
            $table->unique(['shop_id', 'good_id', 'city_id', 'amount', 'measure', 'preorder'], 'unique_package');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
}
