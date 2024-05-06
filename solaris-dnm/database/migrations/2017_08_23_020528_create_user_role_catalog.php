<?php

use Illuminate\Database\Migrations\Migration;

class CreateUserRoleCatalog extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            DB::raw("ALTER TABLE `users` CHANGE `role` `role` ENUM('admin','user','shop','shop_pending','catalog') NOT NULL")
        );

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            DB::raw("ALTER TABLE `users` CHANGE `role` `role` ENUM('admin','user','shop','shop_pending') NOT NULL")
        );
    }
}
