<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shippings', function (Blueprint $table): void {
            $table->uuid('id');
            $table->uuid('product_id');
            $table->string('name');
            $table->decimal('price', 16, 2);
            $table->string('duration', 30);
            $table->integer('from_quantity');
            $table->integer('to_quantity');
            $table->timestamps();
            // key id
            $table->primary('id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shippings');
    }
}
