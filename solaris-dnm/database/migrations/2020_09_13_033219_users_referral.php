<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UsersReferral extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->integer('referrer_id')->after('tg_token')->nullable()->default(null);
            $table->double('referral_fee')->after('referrer_id')->nullable()->default(null);
            $table->index('referrer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
}
