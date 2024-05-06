<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisputeMessagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dispute_messages', function (Blueprint $table): void {
            $table->uuid('id');
            $table->text('message');
            $table->uuid('author_id');
            $table->uuid('dispute_id');
            $table->timestamps();

            $table->primary('id');
            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('dispute_id')->references('id')->on('disputes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispute_messages');
    }
}
