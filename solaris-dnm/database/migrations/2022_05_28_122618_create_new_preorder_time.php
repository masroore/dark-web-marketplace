<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateNewPreorderTime extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE goods_packages MODIFY preorder_time ENUM('24', '48', '72', '480')");
        DB::statement("ALTER TABLE orders MODIFY package_preorder_time ENUM('24', '48', '72', '480')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE goods_packages MODIFY preorder_time ENUM('24', '48', '72')");
        DB::statement("ALTER TABLE orders MODIFY package_preorder_time ENUM('24', '48', '72')");
    }
}
