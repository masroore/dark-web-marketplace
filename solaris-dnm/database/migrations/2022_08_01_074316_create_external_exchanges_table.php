<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalExchangesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('external_exchanges', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->string('payment_id')->unique();
            $table->double('amount', 16, 8);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_exchanges');
    }
}
