<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsGateColumns extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table): void {
            $table->boolean('gate_enabled')->default(false)->after('eos_enabled');
            $table->string('gate_lan_ip')->nullable()->default(null)->after('gate_enabled');
            $table->string('gate_lan_port')->nullable()->default(null)->after('gate_lan_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table): void {
            $table->dropColumn(['gate_enabled', 'gate_lan_ip', 'gate_lan_port']);
        });
    }
}
