<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketFilesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_files', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('user_id')->nullable(false);
            $table->integer('ticket_id')->nullable(false)->index();
            $table->integer('message_id')->nullable(false)->index();
            $table->string('url')->nullable(false);
            $table->timestamp('created_at')->nullable(false)->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_files');
    }
}
