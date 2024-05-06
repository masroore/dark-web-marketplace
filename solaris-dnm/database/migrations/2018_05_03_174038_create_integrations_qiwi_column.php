<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIntegrationsQiwiColumn extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table): void {
            $table->boolean('integrations_qiwi_api')->default(false)->after('integrations_telegram_news');
            $table->string('integrations_qiwi_api_url')->nullable()->default(null)->after('integrations_qiwi_api');
            $table->string('integrations_qiwi_api_key')->nullable()->default(null)->after('integrations_qiwi_api_url');
            $table->text('integrations_qiwi_api_last_response')->nullable()->default(null)->after('integrations_qiwi_api_key');
            $table->timestamp('integrations_qiwi_api_last_sync_at')->nullable()->default(null)->after('integrations_qiwi_api_last_response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table): void {

        });
    }
}
