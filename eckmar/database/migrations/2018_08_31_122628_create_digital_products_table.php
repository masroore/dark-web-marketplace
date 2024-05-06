<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDigitalProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('digital_products', function (Blueprint $table): void {
            $table->uuid('id');
            // digital delivery field
            $table->boolean('autodelivery')->default(true);
            $table->boolean('unlimited')->default(true);
            $table->text('content')->nullable(); // content for autodelivery
            $table->timestamps();

            // keys
            $table->primary('id');
            $table->foreign('id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_products');
    }
}
