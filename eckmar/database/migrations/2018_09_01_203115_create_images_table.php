<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table): void {
            $table->uuid('id');
            $table->uuid('product_id');
            $table->text('image');
            $table->boolean('first');
            $table->timestamps();

            $table->primary('id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade'); // delete images when deleting products
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
}
