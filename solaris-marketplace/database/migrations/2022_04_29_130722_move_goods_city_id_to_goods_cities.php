<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MoveGoodsCityIdToGoodsCities extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $goods = App\Good::all();
        foreach ($goods as $good) {
            App\GoodsCity::create([
                'app_id' => $good->app_id,
                'app_good_id' => $good->app_good_id,
                'city_id' => $good->city_id,
            ]);
        }

        Schema::table('goods', function (Blueprint $table): void {
            $table->dropColumn('city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods', function (Blueprint $table): void {
            $table->integer('city_id')->nullable(false)->after('category_id');
        });
    }
}
