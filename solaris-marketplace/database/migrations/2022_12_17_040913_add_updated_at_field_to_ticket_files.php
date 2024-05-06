<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUpdatedAtFieldToTicketFiles extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ticket_files', function (Blueprint $table): void {
            $table->timestamp('updated_at')->default(Carbon\Carbon::now());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_files', function (Blueprint $table): void {
            $table->dropColumn('updated_at');
        });
    }
}
