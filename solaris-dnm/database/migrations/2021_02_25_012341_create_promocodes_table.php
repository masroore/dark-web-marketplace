<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromocodesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promocodes', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('employee_id');
            $table->string('code');
            $table->enum('discount_mode', ['price', 'percent']);
            $table->decimal('percent_amount', 16, 8)->nullable()->default(null);
            $table->decimal('price_amount', 16, 8)->nullable()->default(null);
            $table->enum('price_currency', ['btc', 'rub', 'usd'])->nullable()->default(null);
            $table->enum('mode', ['single_use', 'until_date']);
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable()->default(null);
            $table->timestamps();

            $table->unique('code');
            $table->index('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promocodes');
    }
}
