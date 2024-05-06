<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class GoodsPackagesPrecision extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        Schema::table('goods_packages', function (Blueprint $table): void {
            $table->decimal('amount', 16, 8)->change();
            $table->decimal('price', 16, 8)->change();
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->decimal('package_amount', 16, 8)->change();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_packages', function (Blueprint $table): void {

        });
    }
}
