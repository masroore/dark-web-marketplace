<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisputesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('dispute_id');
            $table->string('shop_url');
            $table->string('app_id');
            $table->string('creator');
            $table->string('status');
            $table->string('decision');
            $table->string('moderator');
            $table->timestamp('dispute_updated_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
}
