<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomPlacesCityId extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('custom_places', function (Blueprint $table): void {
            $table->boolean('city_id')->nullable()->default(null)->after('good_id')->index();
            $table->index(['good_id', 'city_id']);
            $table->index(['good_id', 'city_id', 'region_id']);
        });

        App\CustomPlace::where('region_id', '<=', 12)->update(['city_id' => 1]);
        App\CustomPlace::where('region_id', '>', 12)->where('region_id', '<=', 30)->update(['city_id' => 3]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table): void {
            $table->dropIndex(['good_id', 'city_id']);
            $table->dropIndex(['good_id', 'city_id', 'region_id']);
            $table->dropColumn('custom_places');
        });
    }
}
