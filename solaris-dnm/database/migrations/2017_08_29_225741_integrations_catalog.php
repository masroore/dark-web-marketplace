<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IntegrationsCatalog extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table): void {
            $table->boolean('integrations_catalog')->default(false)->after('integrations_eos');
        });

        Schema::create('catalog_sync', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('sync_server');
            $table->string('auth_server');
            $table->timestamp('last_sync_at')->nullable();
        });

        App\SyncState::create([
            'sync_server' => config('mm2.catalog_default_sync_server'),
            'auth_server' => config('mm2.catalog_default_auth_server'),
            'last_sync_at' => null,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table): void {
            $table->dropColumn('integrations_catalog');
        });

        Schema::dropIfExists('catalog_sync');
    }
}
