<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsPackagesHasQuests extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('goods_packages', function (Blueprint $table): void {
            $table->tinyInteger('has_quests')->default(0)->after('preorder');
            $table->tinyInteger('has_ready_quests')->default(0)->after('has_quests');
            $table->index(['has_quests', 'has_ready_quests']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_packages', function (Blueprint $table): void {
            $table->dropColumn(['has_quests', 'has_ready_quests']);
        });
    }
}
