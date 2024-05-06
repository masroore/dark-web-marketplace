<?php

use Illuminate\Database\Migrations\Migration;

class CreatePurchasesCancelation extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        $stateQutoed = array_map(fn ($state) => "'$state'", array_keys(App\Purchase::$states));
        $statesStringinfied = implode(',', $stateQutoed);

        // custom statment to add enum value to states of the purchases
        DB::statement("ALTER TABLE purchases MODIFY COLUMN state ENUM($statesStringinfied) DEFAULT 'purchased' NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
}
