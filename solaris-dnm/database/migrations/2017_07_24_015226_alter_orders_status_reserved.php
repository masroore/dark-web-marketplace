<?php

use Illuminate\Database\Migrations\Migration;

class AlterOrdersStatusReserved extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            DB::raw("ALTER TABLE `orders` CHANGE `status` `status` ENUM('preorder_paid','paid','problem','finished','qiwi_reserved','qiwi_paid') NOT NULL")
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            DB::raw("ALTER TABLE `orders` CHANGE `status` `status` ENUM('preorder_paid','paid','problem','finished','reserved','qiwi_paid') NOT NULL")
        );
    }
}
