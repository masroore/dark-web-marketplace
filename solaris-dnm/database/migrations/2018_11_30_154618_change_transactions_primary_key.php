<?php

use Illuminate\Database\Migrations\Migration;

class ChangeTransactionsPrimaryKey extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(
            DB::raw('ALTER TABLE transactions DROP PRIMARY KEY, ADD id INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;')
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
}
