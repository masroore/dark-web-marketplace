<?php

use Illuminate\Database\Migrations\Migration;

class AddStatusesToOrderStatusEnum extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TYPE orders_status ADD VALUE 'cancelled'");
        DB::statement("ALTER TYPE orders_status ADD VALUE 'finished_after_dispute'");
        DB::statement("ALTER TYPE orders_status ADD VALUE 'cancelled_after_dispute'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
}
