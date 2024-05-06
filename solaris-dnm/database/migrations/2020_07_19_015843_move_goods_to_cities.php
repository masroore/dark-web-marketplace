<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MoveGoodsToCities extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $goods = App\Good::all();
        foreach ($goods as $good) {
            App\GoodsCity::create([
                'good_id' => $good->id,
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

    }
}
