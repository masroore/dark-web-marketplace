<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesCityId extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('goods_packages', function (Blueprint $table): void {
            $table->integer('city_id')->after('good_id')->default(0);
            $table->index('city_id');
            $table->index(['good_id', 'city_id']);
        });

        $packages = App\GoodsPackage::with(['good', 'good.cities'])->get();
        foreach ($packages as $package) {
            $good = $package->good;
            $city = $good->cities->first();
            $package->city_id = $city->id;
            $package->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_packages', function (Blueprint $table): void {

        });
    }
}
