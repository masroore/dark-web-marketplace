<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserGroups extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users_groups', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('title');
            $table->enum('mode', ['manual', 'auto']);
            $table->decimal('percent_amount', 16, 8);
            $table->integer('buy_count')->nullable()->default(null);
            $table->timestamps();

            $table->index(['mode', 'buy_count']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->integer('group_id')->after('referral_fee')->nullable()->default(null);
            $table->index('group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
}
