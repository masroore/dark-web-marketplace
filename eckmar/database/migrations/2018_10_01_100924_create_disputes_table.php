<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisputesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table): void {
            $table->uuid('id');
            $table->uuid('winner_id')->nullable();
            $table->timestamps();

            $table->primary('id');
            $table->foreign('winner_id')->references('id')->on('users')->onDelete('set null'); // set null when winner is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
}
