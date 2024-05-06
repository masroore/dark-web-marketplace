<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsPositionsModerated extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('goods_positions', function (Blueprint $table): void {
            $table->boolean('moderated')->default(true)->after('available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_positions', function (Blueprint $table): void {
            $table->dropColumn('moderated');
        });
    }
}
