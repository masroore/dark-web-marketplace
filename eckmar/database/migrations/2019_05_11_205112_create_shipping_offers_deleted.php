<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingOffersDeleted extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // add deleted to offers
        Schema::table('offers', function (Blueprint $table): void {
            $table->boolean('deleted')->default(false);
        });

        // add deleted to shippings
        Schema::table('shippings', function (Blueprint $table): void {
            $table->boolean('deleted')->default(false);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_offers_deleted');
    }
}
