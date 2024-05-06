<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsCities extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('goods_cities', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('good_id');
            $table->integer('city_id');

            $table->index('good_id');
            $table->index('city_id');
            $table->unique(['good_id', 'city_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
}
