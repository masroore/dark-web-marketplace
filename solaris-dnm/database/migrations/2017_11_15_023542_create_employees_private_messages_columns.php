<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesPrivateMessagesColumns extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            $table->boolean('sections_messages_private')->default(false)->after('sections_messages');
            $table->string('sections_messages_private_description')->nullable()->default(null)->after('sections_messages_private');
            $table->boolean('sections_messages_private_autojoin')->default(false)->after('sections_messages_private_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table): void {
            $table->dropColumn(['sections_messages_private', 'sections_messages_private_description', 'sections_messages_private_autojoin']);
        });
    }
}
