<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stats', function (Blueprint $table): void {
            $table->increments('id');
            $table->date('date');
            $table->integer('visitors_count')->default(0);
            $table->string('visitors_data')->nullable()->default(null);
            $table->integer('orders_count')->default(0);
            $table->unique('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stats');
    }
}
