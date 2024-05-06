<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesQiwiAvailable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('goods_packages', function (Blueprint $table): void {
            $table->boolean('qiwi_enabled')->default(false)->after('currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods_packages', function (Blueprint $table): void {
            $table->dropColumn('qiwi_enabled');
        });
    }
}
