<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhysicalProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('physical_products', function (Blueprint $table): void {
            $table->uuid('id');
            // Shipping info
            $table->enum('countries_option', array_keys(App\PhysicalProduct::$countriesOptions))->default('all')->nullable();
            $table->text('countries');
            $table->string('country_from')->nullable();

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
        Schema::dropIfExists('physical_products');
    }
}
