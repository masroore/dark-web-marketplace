<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralUrls extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('referral_urls', function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('slug');
            $table->double('fee');
            $table->timestamps();
            $table->index('user_id');
            $table->index('slug');
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referral_url', function (Blueprint $table): void {

        });
    }
}
