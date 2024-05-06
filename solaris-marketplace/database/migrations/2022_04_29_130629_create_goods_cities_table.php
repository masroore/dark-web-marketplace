<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsCitiesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('goods_cities', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('app_id', 255)->nullable(false)->index();
            $table->integer('app_good_id')->nullable(false)->index();
            $table->integer('city_id')->nullable(false)->index();

            $table->index(['app_id', 'app_good_id']);
            $table->unique(['app_id', 'app_good_id', 'city_id'], 'all_columns_unique_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('goods_cities');
    }
}
