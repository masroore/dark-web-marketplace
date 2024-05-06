<?php

use Illuminate\Database\Migrations\Migration;

class CreateIndividualFeePlan extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            DB::raw("ALTER TABLE `shops` CHANGE `plan` `plan` ENUM('basic', 'advanced', 'individual', 'fee', 'individual_fee') DEFAULT 'basic' NOT NULL")
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(
            DB::raw("ALTER TABLE `shops` CHANGE `plan` `plan` ENUM('basic', 'advanced', 'individual', 'fee') DEFAULT 'basic' NOT NULL")
        );
    }
}
