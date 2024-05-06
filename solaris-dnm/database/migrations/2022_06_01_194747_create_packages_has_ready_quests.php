<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class CreatePackagesHasReadyQuests extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('goods_packages', function (Blueprint $table): void {
            $table->tinyInteger('has_quests')->default(0)->after('employee_penalty');
            $table->tinyInteger('has_ready_quests')->default(0)->after('has_quests');
            $table->index(['has_quests', 'has_ready_quests']);
        });

        Schema::table('goods', function (Blueprint $table): void {
            $table->dropColumn(['has_quests', 'has_ready_quests']);
        });

        Artisan::call('mm2:update_has_quests_cache');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods', function (Blueprint $table): void {
            $table->tinyInteger('has_quests')->default(0)->after('image_url');
            $table->tinyInteger('has_ready_quests')->default(0)->after('has_quests');
            $table->index(['has_quests', 'has_ready_quests']);
        });

        Schema::table('goods_packages', function (Blueprint $table): void {
            $table->dropColumn(['has_quests', 'has_ready_quests']);
        });
    }
}
